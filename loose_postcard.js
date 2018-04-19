var client_id       = loose_php_vars.client_id,
    client_secret   = loose_php_vars.client_secret,
    access_token    = loose_php_vars.access_token,
		num_photos;

jQuery(document).ready(function() {
	if ( jQuery('.loosePostcard.target').data("display-num") ) {
		num_photos    = jQuery('.loosePostcard.target').data("display-num");
	} else {
		num_photos		=	2;
	}

	jQuery.ajax({
		url: 'https://api.instagram.com/v1/users/self/media/recent/?access_access_token' + access_token,
		dataType: 'jsonp',
		type: 'GET',
		data: {
			access_token: access_token,
			count: num_photos
		},
		success: function(data){
			for (var i = 0; i < data.data.length; i++) {
				var postTargetId = "#postCard" + i;
				createPostTarget(i);
				displayUserInfo(postTargetId, data.data[i]);
				displayPhoto(postTargetId, data.data[i]);
				displayPhotoInfo(postTargetId, data.data[i]);
			}
		},
		error: function(data){
			console.log(data);
		}
	});

	function displayPhoto(postTarget, photo_object) {
		jQuery(postTarget).append(
			'<div class="photo">'
				+ '<img src="' + photo_object.images.standard_resolution.url + '">'
			+ '</div>'
		);
	}

	function displayUserInfo(postTarget, photo_object) {
		jQuery(postTarget).append(
			'<div class="userInfo">'
					+ '<div class="profilePicture">'
						+	'<a href="https://wwww.instagram.com/' + photo_object.user.username+ '"><img src="' + photo_object.user.profile_picture + '" target="_blank"></a>'
					+ '</div>'
					+ '<div class="info">'
						+ 'Posted by <a class="userName" href="https://www.instagram.com/' + photo_object.user.username + '" target="_blank">' + photo_object.user.username + '</a>'
						+ '<span class="createdTime"> on ' + parseUnixTime(photo_object.created_time) + '</span>'
					+ '</div>'
				+ '</div>'
		);
	}

	function displayPhotoInfo(postTarget, photo_object) {
		jQuery(postTarget).append(
			'<div class="photoInfo">'
				+	'<div class="likes">' + displayLikes(photo_object) + '</div>'
				+	'<div class="caption">' + displayCaption(photo_object) + '</div>'
				+	'<div class="tags">' + displayTags(photo_object) + '</div>'
			+	'</div>'
		);
	}

	function createPostTarget(index) {
		jQuery('.loosePostcard.target').append('<div id="postCard' + index + '" class="postCard"></div>')
	}

	function parseUnixTime(unixTime) {
		var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
		var date	= new Date(unixTime * 1000),
				year	=	date.getFullYear(),
				month	= months[date.getMonth()],
				day 	= date.getDate();

		return month + ' ' + day + ', ' + year;
	}

	function displayLikes(photo_object) {
		if (photo_object.likes.count == 1) {
			return photo_object.likes.count + " like";
		} else {
			return photo_object.likes.count + " likes";
		}
	}

	function displayCaption(photo_object) {
		var captionArray	= photo_object.caption.text.split(" "),
				captionString	=	"";
		for (var i = 0; i < captionArray.length; i++) {
			if (captionArray[i].charAt(0) !== "#") {
				captionString += captionArray[i] + " ";
			}
		}
		return captionString;
	}

	function displayTags(photo_object) {
		var tagsArray  = photo_object.tags,
				tagsString = "";
		for (var i = 0; i < tagsArray.length; i++) {
			tagsString += '<a href="https://www.instagram.com/explore/tags/' + tagsArray[i] + '" target="_blank">#' + tagsArray[i] + '</a> ';
		}
		return tagsString;
	}

}); // end
