/**
 * Redirect user after pressing the publish or save draft button.
 */
let wcGutenbergEditor = window.wp.data.dispatch('core/editor');
if ( wcGutenbergEditor ) {
    let savePost = wcGutenbergEditor.savePost;

    wcGutenbergEditor.savePost = function(options) {
        options = options || {};

        return savePost(options).then(() => {
            if ( ! options.isAutosave ) {
                const failed   = wp.data.select('core/editor').didPostSaveRequestFail();
                if ( ! failed ) {
                    if ( typeof wcEditorRedirect.length !== "undefined" ) {
                        window.location.href = wcEditorRedirect.overviewUrl;
                        return;
                    }

                    const post_type = wp.data.select('core/editor').getCurrentPostType();
                    window.location.href = '/wp-admin/edit.php?post_type=' + post_type;
                }
            }
        });
    };
}