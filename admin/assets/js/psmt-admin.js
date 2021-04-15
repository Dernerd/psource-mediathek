/**
 * PsourceMediathek Admin Management Js
 * Loaded on the Edit Gallery page
 */
jQuery( document ).ready( function(){

    var jq = jQuery;
    // Disable the browser keeping delete selected when user selects it and reloads page
    // this beauty will save our users any misfortune.
    if( jq('#psmt-edit-media-bulk-action').get(0) ) {
        jq('#psmt-edit-media-bulk-action').val('');
    }

    // The following code saves the server any headache caused by overloading
    // We provide you the best experience and for that, let us sacrifice the contents of gallery panel
    // Do not send any gallery data from panels when WordPress post publish button is clicked
    jq( 'form#post' ).submit( function() {
        jq( this ).find( '#psmt-admin-edit-panels' ).remove();
    });

    // hilight current tab.
    if ( _psmtData.current_type ) {

        var $current_menu = jQuery( '#menu-posts-psmt-gallery' ).find( 'ul li a[href*="psmt-gallery-type=' + _psmtData.current_type+'"]' );

        if ( $current_menu.parent( 'li' ) ) {
            // detect other
            jQuery( '#menu-posts-psmt-gallery' ).find( 'ul li.wp-first-item' ).removeClass( 'current' );
            $current_menu.parent().addClass('current');
        }
    }
    // notify is the function that gives any global notification
    // If you are a theme author, you can redefine it to give better feedback
    psmt.notify = function( message, error ) {
        // notify message inside the active panel
        var class_name = 'updated';
        if ( error !== undefined ) {
            class_name = 'error';
        }
        jq('#message').remove();// will it have side effects?
        var container_selector = '#psmt-admin-edit-panels .psmt-admin-active-panel';
        jq( container_selector ).prepend( '<div id="message" class="bp-template-notice psmt-template-notice ' + class_name + '"><p>'+message +'</p></div>').show();
    };

    function psmt_create_admin_uploader() {
        // A custom implementation of plupload uploader
        // base on WordPress Media Uploader
        // The opensource makes this community beautiful
        psmt.admin_uploader = new psmt.Uploader({
            container: 'body',
            dropzone: '#psmt-upload-dropzone-admin',
            browser: '#psmt-upload-media-button-admin',
            feedback: '#psmt-upload-feedback-admin',
            media_list: '#psmt-uploaded-media-list-admin',//where we will list the media
            uploading_media_list : _.template ( "<li id='<%= id %>'><span class='psmt-attached-file-name'><%= name %></span>(<span class='psmt-attached-file-size'><%= size %></spa>)<span class='psmt-remove-file-attachment'>x</span> <b></b></li>" ),
            uploaded_media_list : _.template ( "<li class='psmt-uploaded-media-item' id='psmt-uploaded-media-item-<%= id %>' data-media-id='<%= id %>'><img src='<%= url %>' /><a href='#' class='psmt-delete-uploaded-media-item'>x</a></li>" ),

            success:  function( file ) {
                // when a file has been successfully uploaded
                var sizes = file.get( 'sizes' );
                // var original_url = file.get('url');
                var id = file.get('id');
                var file_obj = file.get('file');
                var thumbnail ='';

                if ( sizes !== undefined ) {
                    thumbnail = sizes.thumbnail;
                } else if( file.get('thumb') ) {
                    thumbnail = file.get('thumb');
                }

                var html = this.uploaded_media_list({id:id, url: thumbnail.url, });

                jq(this.feedback).find('li#'+file_obj.id ).remove();

                jq('ul', this.media_list).append( html);
                //save in cookie
                //psmt_add_media_to_cookie( id );
                ////this is not required for admin uploads, we only need it for activity uploader
                //We will remove the function call from here when we feel like it

            },

            hide_ui : function() {
                // when our upload UI is to be hidden
                this.clear_media_list();
                this.clear_feedback();
                this.hide_dropzone();
            },

            init: function () {
                // the uploader got initialized
                // this.clear_media_list();
                jq( 'ul', this.media_list ).append( jq( '#psmt-loader-wrapper').clone());
            },

            isRestricted: function ( uploader, file ) {
                // this method provides us a way to restrict any upload
                return false;
            },

            error: function( reason, data, file ) {
                // When type is not matched for selected files in the file browser
                // this error will request our awesome site owner friend to choose the file types from given extensions
                if ( data && data.code == '-601' ) {
                    psmt.notify( _psmtData.type_errors[_psmtData.current_type], 'error' );
                }
                // this is used when a file upload fails for some reason
                // we love helpful people and we are trying to be helpful here too.
                if ( this.feedback ) {
                    jq('ul li#'+file.id, this.feedback ).addClass('psmt-upload-fail').find('b').html('<span>' + reason + "</span>");
                }
            },
            complete: function () {

                // hide the loader
                if (  this.media_list ) {
                    jq( '.psmt-loader', this.media_list ).hide();
                }

                // reload edit panel
                psmt_admin_reload_edit_media_panel();
            }
        });

        // setup the current file type for uploader
        // marry the uploader to the file extensions, how nice that is.
        psmt_setup_uploader_file_types( psmt.admin_uploader );

        // what is a marriage without a context, bride, grooms and guests, all are important
        // let us give some context to our uploader
        if ( jq( '#psmt-upload-dropzone-admin' ).get(0) ) {
            psmt.admin_uploader.param( 'context', 'admin' );
            psmt.admin_uploader.param( 'gallery_id', jq( '#post_ID' ).val() );
        }

    }
    psmt_create_admin_uploader();

    // Trigger delete, deletes any trace of a Media
    jq( document ).on( 'click', '.psmt-uploading-media-list .psmt-delete-uploaded-media-item', function () {

        var $this =jq( this );
        var $parent = jq( $this.parent() ); //parents are very important in our life, how can we forget them
        // is the data-media-id attribute set, like parents keep their child in heart, our $parent does too
        var id = $parent.data( 'media-id' );

        if ( ! id ) {
            return false;
        }
        // show the round round round loader, It shows the loader gif
        show_loader();

        // get the security pass for clearance because unidentified intruders are not welcome in the family.
        var nonce = jq('#_psmt_manage_gallery_nonce').val();

        // Now is the time to take action,
        jq.post( ajaxurl, {
            action: 'psmt_delete_media',
            media_id: id,
            cookie: encodeURIComponent( document.cookie ),
            _wpnonce: nonce
        }, function ( response ) {
            // how rude the nature is
            // you deleted my media and still sending me message
            if ( response.success !== undefined ) {
                $parent.remove(); // can't believe the parent is going away too

                // psmt_remove_media_from_cookie(id);
                psmt.notify( response.message ); // let the superman know what consequence his action has brought

            } else {
                // something went wrong, perhaps the media escaped the deletion
                psmt.notify( response.message );
            }
            // enough, let us hide the round round feedback
            hide_loader();

        }, 'json' );

        return false;
    });

    function psmt_admin_enable_sorting() {
        // this is what I call too many child problem
        // This allows the caretaker to arrange them the way they want
        if ( jq.fn.sortable !== undefined ) {

            jq("#psmt-uploaded-media-list-admin>ul").sortable({
                opacity: 0.6,
                cursor: 'move',
                stop: function (evt, ui ) {
                    var sorted = jq("#psmt-uploaded-media-list-admin>ul").sortable('serialize', {key: 'psmt-media-ids[]'});
                    psmt_update_sorting( sorted );
                }
            });
        }
    }
    psmt_admin_enable_sorting();

    /**
     * Updates the sorting order
     * @param {type} ids
     * @returns {undefined}
     */
    function psmt_update_sorting( ids ) {

        if ( ! ids ) {
            return ;
        }

        show_loader();

        var nonce = jq( '#_psmt_manage_gallery_nonce' ).val();
        var data = ids + '&action=psmt_reorder_media&_wpnonce='+nonce;

        jq.post( ajaxurl, data, function( response ) {

            if ( response.success !== undefined ) {
                psmt.notify( response.message );
            } else {
                psmt.notify( response.message, 'error' );
            }

            hide_loader();

        } );

    }

    // bulk edit
    // allows us to rename the media, bulk delete them and change their privacy etc
    // anything that your do from PsourceMediathek->Add/Edit Media -> Edit Media panel is handled by
    jq( document ).on( 'click', '#psmt-edit-media-submit, #bulk-action-apply', function () {

        // check if delete action in bulk selected
        // This will nuke all media, and we know that nuke is not good for humanity.
        // let us confirm our president again, if they really want to do it?
        if ( jQuery('#psmt-edit-media-bulk-action').val() === 'delete' ) {
            if( ! confirm( _psmtStrings.bulk_delete_warning ) ) {
                return false;
            }
        }

        show_loader();

        var gallery_id = jq('#post_ID').val();

        // var $this = jq( this );
        // find our parent
        var $parent = jq( jq( 'form#post' ).find( '#psmt-media-bulkedit-div' ) );
        var data = $parent.find('input, textarea, select').serialize(); // get second form element
        var nonce = jq('#_psmt_manage_gallery_nonce').val();
        //
        // let us build the data that we send to our server
        // many place we are using serialized array to keep any data added by addons to be part of it

        data = data +'&gallery_id=' + gallery_id + '&action=psmt_bulk_update_media&_wpnonce=' + nonce;

        jq.post( ajaxurl, data, function( response ) {

            if ( response.success !== undefined ) {

                jq('#psmt-admin-edit-panel-tab-edit-media').html( response.contents );
                psmt.notify( response.message );
                // reload add media panel to reflect the change
                psmt_admin_reload_add_media_panel();

            } else {
                // notify
                psmt.notify( response.message, 'error' );
            }

            hide_loader();
        });

        return false;
    });

    // cover delete
    jq( document ).on( 'click', '#psmt-cover-delete', function () {

        var gallery_id = jq('#post_ID').val();

        if ( ! gallery_id ) {
            return false;
        }

        var nonce = jq('#_psmt_manage_gallery_nonce').val();

        show_loader();

        jq.post( ajaxurl, {
            action: 'psmt_delete_gallery_cover',
            gallery_id: gallery_id,
            _wpnonce: nonce,
            cookie: encodeURIComponent( document.cookie )
        },  function (response ) {

            if ( response.success !== undefined ) {
                // delete cover, replace with default
                jq( '#psmt-cover-' + gallery_id ).find( '.psmt-cover-image' ).attr( 'src', response.cover );
                psmt.notify( response.message );

            } else {
                // notify
                psmt.notify( response.message, 'error' );
            }

            hide_loader();

        }, 'json');

        return false;
    });


    // cover delete
    jq( document ).on( 'click', '#psmt-update-gallery-details', function () {
        var $parent = jq( jq( 'form#post').find( '#psmt-gallery-edit-form' ) );

        var data = $parent.find( 'input, textarea, select' ).serialize(); // get second form element
        var nonce = jq( '#_psmt_manage_gallery_nonce' ).val();

        data = data + '&action=psmt_update_gallery_details&_wpnonce=' + nonce;

        show_loader();

        jq.post( ajaxurl, data,  function ( response ) {

            if ( response.success !== undefined ) {
                psmt.notify( response.message );
            } else {
                // notify
                psmt.notify( response.message, 'error' );
            }

            hide_loader();

        }, 'json');

        return false;
    });


    // Reload edit panel
    jq( document ).on( 'click', '#psmt-reload-bulk-edit-tab', function () {
        psmt_admin_reload_edit_media_panel();
        return false;
    });

    // Reload upload contents
    jq( document ).on( 'click', '#psmt-reload-add-media-tab', function () {
        psmt_admin_reload_add_media_panel();
        return false;
    });

    function psmt_admin_reload_edit_media_panel() {
        psmt_admin_reload_edit_panel( '#psmt-admin-edit-panel-tab-edit-media', 'psmt_reload_bulk_edit' );
    }

    // sometimes, you can understand a lot about people by reading their names
    function psmt_admin_reload_add_media_panel() {
        var $tab = '#psmt-admin-edit-panel-tab-add-media';
        var gallery_id = jq('#post_ID').val();
        var nonce = jq('#_psmt_manage_gallery_nonce').val();

        $tab = jq( $tab );//to reload under this tab

        var loader = jq( '#psmt-loader-wrapper' ).clone();
        $tab.find( '#psmt-show-loader' ).remove();
        $tab.prepend( '<ul id="psmt-show-loader"></ul>' );
        $tab.find( '#psmt-show-loader' ).append( loader.show() );

        jq.post( ajaxurl, {
            action: 'psmt_reload_add_media',
            gallery_id: gallery_id,
            _wpnonce: nonce

        }, function (response ) {

            if ( response.success !== undefined ) {
                // $tab.empty();
                $tab.html( response.contents );
                // reattach uploader
                // or should we first destroy earlier uploader before reattaching?
                psmt_create_admin_uploader();
                psmt_admin_enable_sorting();
                if ( response.message !== undefined ) {
                    psmt.notify( response.message );
                }
            } else {
                psmt.notify( response.message, 1 );
            }

            $tab.find( '#psmt-show-loader' ).remove();
        }, 'json');
    }


    function psmt_admin_reload_edit_panel( $tab, action, gallery_id, nonce ) {

        if ( ! gallery_id ) {
            gallery_id = jq('#post_ID').val();
        }

        if ( ! nonce ) {
            nonce = jq('#_psmt_manage_gallery_nonce').val();
        }

        $tab = jq( $tab );//to reload under this tab

        var loader = jq( '#psmt-loader-wrapper' ).clone();
        $tab.find( '#psmt-show-loader' ).remove();
        $tab.prepend( '<ul id="psmt-show-loader"></ul>' );
        $tab.find( '#psmt-show-loader' ).append( loader.show() );

        jq.post( ajaxurl, {
            action: action,
            gallery_id: gallery_id,
            _wpnonce: nonce

        }, function (response ) {

            if ( response.success !== undefined ) {

                //$tab.empty();
                $tab.html( response.contents );

                if( response.message !== undefined ) {
                    psmt.notify( response.message );
                }
            } else {
                psmt.notify( response.message, 1 );
            }


            $tab.find( '#psmt-show-loader' ).remove();
        }, 'json');
    }

    function show_loader() {

        var loader = jq( '#psmt-loader-wrapper' ).clone();
        jq( '#psmt-show-loader' ).remove();// will it have side effects?
        var container_selector = '.psmt-admin-active-panel' ;
        jq( container_selector ).prepend( '<ul id="psmt-show-loader"></ul>' );
        jq( '#psmt-show-loader' ).append( loader.show() );

    }

    function hide_loader() {
        jq( '#psmt-show-loader' ).remove();//
    }
    // Autobots, assemble.

} );
