/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */
try {
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
window.axios = require('axios');

// set dropzone globally
window.Dropzone = require('dropzone');

// disable dropzone auto discover to prevent it from declared twice
Dropzone.autoDiscover = false;

// init tooltip
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

// set attachment manager globally
window.attachmentManager = {
    bindOpenInNewTab(file, url) {
        // unbind default click handler
        $(file.previewElement).unbind('click');

        // on thumbnail click create url from file
        $(file.previewElement).on('click', function () {
            window.open(url, '_blank');
        });
    },
    getIcon(file) {
        // get the last word after dot
        let ext = file.name.split('.').pop().toLowerCase();

        let icon = 'txt';

        if (ext === 'docx' || ext === 'doc') {
            icon = 'doc';
        } else if (ext === 'xlsx' || ext === 'xls') {
            icon = 'xls';
        } else if (ext === 'pdf') {
            icon = 'pdf';
        } else if (ext === 'csv') {
            icon = 'csv';
        }

        return `/storage/images/icons/${icon}.svg`;
    },
    update(dropzone, uploadedFiles, uploadInputs) {
        uploadedFiles.content.forEach(attachment => {
            // define the mock file
            const mockFile = {
                id: attachment.id,
                name: attachment.name,
                size: attachment.size,
                type: attachment.type,
                dataURL: attachment.url || attachment.dataURL,
                accepted: true
            };

            // trigger addedfile event by mock file
            dropzone.emit('addedfile', mockFile);

            // determine whether file is image
            if (mockFile.type.match(/image*/)) {
                // download image then resize it to create the thumbnail
                dropzone.createThumbnailFromUrl(
                    mockFile,
                    dropzone.options.thumbnailWidth,
                    dropzone.options.thumbnailHeight,
                    dropzone.options.thumbnailMethod,
                    true,
                    thumbnail => dropzone.emit('thumbnail', mockFile, thumbnail)
                );
            } else {
                // get icon to create the thumbnail
                dropzone.emit('thumbnail', mockFile, this.getIcon(mockFile));
            }

            // trigger complete event by mock file
            dropzone.emit('complete', mockFile);

            // this line needed to ensure max files exceeded event is working
            dropzone.files.push(mockFile);

            // create a new input hidden with value is mock file's id
            uploadInputs.create(mockFile.id);

            this.bindOpenInNewTab(mockFile, mockFile.dataURL);
        });
    }
};
