<template>
    <div>
        <input id="trix" type="hidden" :name="name" :value="value">
        <trix-editor class="trix-content"
            input="trix"
            @trix-change="change"
            @trix-attachment-add="uploadAttachmentFile"></trix-editor>
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

            preventDefault(e) {
                e.preventDefault();
            },

            uploadAttachmentFile(e) {
                if (! e.attachment.file || e.attachment.file.size > 512 * 1024) {
                    return;
                }

                let attachment = e.attachment;

                console.log(attachment.file.size);

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
                            var progress = event.loaded / event.total * 100;
                            progressCallback(progress);
                        }
                    });

                let data = new FormData;
                data.append('image', file);

                request.post('/api/threads/image', data)
                    .then(response => {
                        let attributes = {
                            "url": response.data.url,
                            "filePath": response.data.filePath
                        };

                        successCallback(attributes);
                    });
            }
        }
    }
</script>
