<template>
    <div>
        <input :id="id" type="hidden" :name="name" :value="value">
        <trix-editor ref="editor"
            class="trix-content"
            :id="trixId"
            :input="id"
            @trix-change="change"
            @trix-file-accept="check"
            @trix-attachment-add="uploadAttachmentFile"
            @trix-attachment-remove="removeAttachmentFile"></trix-editor>
    </div>
</template>

<script>
    import Trix from "trix";

    export default {
        props: ['id', 'name', 'value', 'trixPersist', 'acceptFile'],

        data() {
            return {
                persistList: [], // 用來記錄要與db同步的圖片
                deleteList: [] // 用來紀錄將被刪除的圖片
            };
        },

        computed: {
            trixId() {
                return 'trix-' + this.id;
            }
        },

        mounted() {
            this.$emit('trix-mounted', {'id': this.trixId});
        },

        watch: {
            trixPersist: function () {
                if (! this.trixPersist) return;

                this.persist();
                this.deletePersist();
            }
        },

        methods: {
            change(e) {
                this.$emit('trix-change', {"value": e.target.innerHTML});
            },

            check(e) {
                if (this.acceptFile === "false") {
                    this.cancelUpload(e);
                    return;
                }

                this.$emit('trix-file-accept', e);

                if (! e.file || e.file.size > 512 * 1024) {
                    flash("Image size must be less than or equals to 512KB", 'danger');
                    e.preventDefault();
                }
            },

            uploadAttachmentFile(e) {
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
                    .then(({data}) => {
                        this.persistList.push(data.cacheKey);
                        delete data.cacheKey;

                        successCallback(data);
                    });
            },

            persist() {

                if (this.persistList.length > 0) {
                    axios.patch('/api/images/trix', {"persistList": this.persistList});
                }

                this.$emit('persist-complete');
            },

            removeAttachmentFile(e) {
                let filePath = e.attachment.attachment.attributes.values.filePath;

                this.deleteList.push(filePath);
            },

            deletePersist() {

                if (this.deleteList.length > 0) {
                    axios.delete('/api/images/trix', { "data": { "images": this.deleteList } });
                }

                this.$emit('persist-complete');
            },

            cancelUpload(e) {
                e.preventDefault();
                flash('目前reply不提供上傳圖片的功能 !!', 'danger');
            },

            cleanContent() {
                let editor = this.$refs.editor.editor;
                let range = [0, editor.getSelectedRange()[1]];
                editor.setSelectedRange(range);
                editor.deleteInDirection('backward');
            }
        }
    }
</script>
