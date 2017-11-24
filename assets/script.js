var video_uploader = null;

function open_media_uploader_video() {
    video_uploader = wp.media({
        frame:    "video",
        state:    "video-details",
    });

    video_uploader.on("update", function(){
        var extension = video_uploader.state().media.extension;
        var video_url = video_uploader.state().media.attachment.changed.url;
        var video_icon = video_uploader.state().media.attachment.changed.icon;
        var video_title = video_uploader.state().media.attachment.changed.title;
        var video_desc = video_uploader.state().media.attachment.changed.description;
    });

    video_uploader.open();
}