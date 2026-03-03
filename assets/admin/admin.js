// WebWeaver Admin Scripts

(function ($) {
    // API helper.
    window.webweaverApi = {
        getTools: function (callback) {
            $.ajax({
                url: webweaverAdmin.apiUrl + '/tools',
                headers: {
                    'X-WP-Nonce': webweaverAdmin.nonce,
                },
                success: callback,
            });
        },

        listPosts: function (params, callback) {
            $.ajax({
                url: webweaverAdmin.apiUrl + '/posts',
                data: params,
                headers: {
                    'X-WP-Nonce': webweaverAdmin.nonce,
                },
                success: callback,
            });
        },

        createPost: function (data, callback) {
            $.ajax({
                url: webweaverAdmin.apiUrl + '/post',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(data),
                headers: {
                    'X-WP-Nonce': webweaverAdmin.nonce,
                },
                success: callback,
            });
        },

        updatePost: function (postId, data, callback) {
            $.ajax({
                url: webweaverAdmin.apiUrl + '/post/' + postId,
                method: 'PUT',
                contentType: 'application/json',
                data: JSON.stringify(data),
                headers: {
                    'X-WP-Nonce': webweaverAdmin.nonce,
                },
                success: callback,
            });
        },
    };

    // Document ready.
    $(document).ready(function () {
        console.log('WebWeaver Admin loaded');
    });
})(jQuery);
