import $ from 'jquery';

const deleteAction = (function () {
    let _ = {
        $deleteItem: $('.delete-item'),
        $deleteItemConfirm: $('.delete-item-confirm'),
        siteId: 0,
        postId: 0,

        showConfirmModal: {
            init: () => _.$deleteItem.on('click', _.showConfirmModal.setIds),
            setIds: function () {
                _.siteId = $(this).data('site-id');
                _.postId = $(this).data('post-id');
                console.log(_.postId);
            },
        },
        confirmDelete : {
            init: () => _.$deleteItemConfirm.on('click', _.confirmDelete.confirmDelete),
            confirmDelete: function() {
                $.ajax({
                    type: 'GET',
                    url: '/post/' + _.siteId + '/delete/' + _.postId,
                    success: function() {
                        window.location.reload();
                    }
                });
            }
        }
    }
    return {
        init: function () {
            _.showConfirmModal.init();
            _.confirmDelete.init();
        }
    }
});

$(function () {
    deleteAction().init();
})