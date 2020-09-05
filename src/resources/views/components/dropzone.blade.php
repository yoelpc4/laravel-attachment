<div class="form-group">
    <label for="{{ $name }}">{{ $label }}</label>
    <div class="dropzone-container">
        <div id="{{ $name }}" class="dropzone" data-toggle="tooltip" data-html="true" title="{{ $slot }}">
            <div class="dz-message" data-dz-message>
                <b>Drop file(s) here to upload</b>
            </div>
        </div>
    </div>
    <div class="dropzone-button-container">
        <button type="button" class="btn btn-secondary hint" data-toggle="tooltip" data-html="true" title="{{ $slot }}">
            <i class="fa fa-info-circle"></i>
        </button>
        <button type="button" id="{{ $name }}_button" class="btn btn-primary upload">
            <i class="fa fa-upload"></i> Upload
        </button>
    </div>
</div>
<div class="clearfix"></div>

@push('component_scripts')
    <script>
        $(function () {
            window['{{ $name }}'] = {
                files: {
                    content: [],
                    create(attachment) {
                        this.content.push(attachment);

                        return this;
                    },
                    find(id) {
                        return this.content.find(item => item.id === id);
                    },
                    delete(id) {
                        this.content.forEach((item, index, arr) => {
                            if (item.id === id) {
                                arr.splice(index, 1);
                            }
                        });

                        return this;
                    },
                    store() {
                        sessionStorage.setItem('{{ $name }}', JSON.stringify(this.content));
                    },
                    fetch() {
                        return JSON.parse(sessionStorage.getItem('{{ $name }}'));
                    }
                },
                button: {
                    el: $('#{{ $name }}_button'),
                    set(el) {
                        this.el = el;
                    },
                    show() {
                        this.el.show()
                    },
                    hide() {
                        this.el.hide()
                    },
                    registerConfirmation(dropzone) {
                        let el = this.el;

                        el.on('click', function () {
                            // if confirmed upload all files in queue
                            dropzone.processQueue();

                            // hide button
                            el.hide();
                        });
                    }
                },
                inputs: {
                    dropzoneEl: $('#{{ $name }}'),
                    create(value) {
                        this.dropzoneEl.append(
                            $('<input>', {
                                type: 'hidden',
                                name: '{{ $name }}_ids[]',
                                value: value
                            })
                        );
                    },
                    delete(id) {
                        this.dropzoneEl.find('input').each(function () {
                            // remove input if file id equals to input value
                            if (parseInt($(this).val()) === id) {
                                $(this).remove();
                            }
                        });
                    }
                }
            };

            window['{{ $name }}'].dropzone = new Dropzone('#{{ $name }}', {
                url: '{{ route('attachments.store') }}',
                addRemoveLinks: @json($removeable),
                acceptedFiles: @json($extensions), // allowed file extensions with comma as delimiter
                maxFilesize: @json($maxFileSize ?? 10), // in mb
                maxFiles: @json($maxFiles ?? 1),
                parallelUploads: @json($maxFiles ?? 1),
                autoProcessQueue: false,
                paramName: 'attachment', // request key for uploaded file
                params: {
                    attachable_type: @json($attachable_type),
                    attachable_id: @json($attachable_id),
                    file_attachment: @json($file_attachment)
                },
                headers: {
                    accept: 'application/json',
                    'X-CSRF-Token': document.head.querySelector('meta[name="csrf-token"]').content
                },
                init() {
                    const dropzone = this;

                    // running on request validation error
                    if (@json($errors->any())) {
                        // fetch uploaded files from session storage
                        const attachments = window['{{ $name }}'].files.fetch();

                        if (attachments !== null) {
                            // if attachments exists then sets it to uploaded files
                            window['{{ $name }}'].files.content = attachments;

                            attachmentManager.update(dropzone, window['{{ $name }}'].files, window['{{ $name }}'].inputs);
                        }
                    } else if (@json($attachments)) {
                        // fill attachments
                        window['{{ $name }}'].files.content = @json($attachments);

                        window['{{ $name }}'].files.store();

                        attachmentManager.update(dropzone, window['{{ $name }}'].files, window['{{ $name }}'].inputs);
                    }

                    window['{{ $name }}'].button.registerConfirmation(dropzone);

                    this.on('addedfile', function (file) {
                        // attach thumbnail to file's preview element's image
                        $(file.previewElement).find('.dz-image img').attr('src', attachmentManager.getIcon(file));

                        attachmentManager.bindOpenInNewTab(file, URL.createObjectURL(file));

                        window['{{ $name }}'].button.show();
                    });

                    this.on('success', function (file, response) {
                        const data = response.data;

                        // this line is needed to delete file by id purpose
                        file.id = data.id;

                        // fill uploaded files arr with data's attribute
                        const attachment = {
                            id: data.id,
                            name: data.name,
                            size: data.size,
                            type: data.type,
                            dataURL: data.url
                        };

                        // append a new input hidden with value is data's id
                        window['{{ $name }}'].inputs.create(data.id);

                        window['{{ $name }}'].files.create(attachment).store();

                        attachmentManager.bindOpenInNewTab(file, data.url);
                    });

                    this.on('removedfile', function (file) {
                        const id = file.id;

                        // get uploaded file from uploaded files arr
                        const attachment = window['{{ $name }}'].files.find(id);

                        // if file exists in uploaded files arr
                        // then deletes it from server
                        if (attachment) {
                            axios.delete(`/attachments/${id}`)
                                .then(response => {
                                    console.log(response.data.message);

                                    window['{{ $name }}'].files.delete(id).store();

                                    window['{{ $name }}'].inputs.delete(id);
                                })
                                .catch(error => {
                                    console.log(error);
                                });
                        }

                        if (!dropzone.files.filter(file => !file.accepted).length) {
                            window['{{ $name }}'].button.show();
                        } else {
                            window['{{ $name }}'].button.hide();
                        }

                        // if this doesnt have queued files hide upload button
                        if (!dropzone.getQueuedFiles().length) {
                            window['{{ $name }}'].button.hide();
                        }
                    });

                    this.on('error', function (file, error) {
                        console.log(error);

                        let message = error.message;

                        // get laravel's errors message bag
                        const errors = error.errors;

                        // if errors isn't undefined process it
                        if (errors) {
                            // if exists then looping foreach errors properties
                            for (let property in errors) {
                                // determine whether errors has the following property
                                if (errors.hasOwnProperty(property)) {
                                    // get the first errors message by property then set it to message
                                    message = errors[property][0];
                                    // break the looping on first errors message encounters
                                    break;
                                }
                            }
                        }

                        // display error message on dropzone
                        $(file.previewElement).find('.dz-error-message').text(message);

                        window['{{ $name }}'].button.hide();
                    });
                }
            });
        });
    </script>
@endpush
