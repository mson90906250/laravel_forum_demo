<template>
    <div>
        <input id="trix" type="hidden" :name="name" :value="value">
        <trix-editor class="trix-content"
            input="trix"
            @trix-change="change"
            @trix-file-accept="check"
            @trix-attachment-add="uploadAttachmentFile"
            @trix-attachment-remove="removeAttachmentFile"></trix-editor>
    </div>
</template>

<script>
    import Trix from "trix";

    export default {
        props: ['name', 'value'],

        methods: {
            change(e) {
                this.$emit('trix-change', {"value": e.target.innerHTML});
            },

            check(e) {
                if (e.file.size > 512 * 1024) {
                    flash("Image size must be less than or equals to 512KB", 'warning');
                    e.preventDefault();
                }
            },

            uploadAttachmentFile(e) {
                if (! e.attachment.file || e.attachment.file.size > 512 * 1024) return;

                let attachment = e.attachment;

                this.uploadFile(attachment.file, setProgress, setAttributes)

                function setProgress(progress) {
                    attachment.setUploadProgress(progress)
                }

                function setAttributes(attributes) {
                    attachment.setAttributes(attributes)
                }
            },

            uploadFile(file, progressCallback, successCallback) {

                let request = axios.create({
                        onUploadProgress(e) {
                            let progress = event.loaded / event.total * 100;
                            progressCallback(progress);
                        }
                    });

                let data = new FormData;
                data.append('image', file);

                request.post('/api/images/trix', data)
                    .then(response => {
                        successCallback(response.data);
                    });
            },

            removeAttachmentFile(e) {
                let filePath = e.attachment.attachment.attributes.values.filePath;

                axios.delete('/api/images/trix', { "data": { "image": filePath } })
                    .then(response => {
                        console.log(response);
                    });
            }
        }
    }
</script>
