/**
 * Redirect user after pressing the publish or save draft button.
 */
let gutenbergEditor = window.wp.data.dispatch('core/editor');
if ( gutenbergEditor ) {
    let savePost = gutenbergEditor.savePost;

    gutenbergEditor.savePost = function(options) {
        options = options || {};

        return savePost(options).then(() => {
            if ( ! options.isAutosave ) {
                const failed   = wp.data.select('core/editor').didPostSaveRequestFail();
                if ( ! failed ) {
                    if ( typeof editorRedirect.length !== "undefined" ) {
                        window.location.href = editorRedirect.overviewUrl;
                        return;
                    }

                    const post_type = wp.data.select('core/editor').getCurrentPostType();
                    window.location.href = '/wp-admin/edit.php?post_type=' + post_type;
                }
            }
        });
    };
}