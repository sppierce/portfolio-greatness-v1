var igalleryClass ={

	Implements: Options,

    options: {},

	initialize: function(options)
	{
		this.setOptions(options);
		this.imageIndex = this.options.activeImage;
		this.currentHash = '';
		this.zIndex = 0;
		this.lastImageDisplayedIndex = -1;
		this.lightboxShowCounter = 0;
        this.imageShowCounter = 0;
		this.lboxPreloadStarted = false;
        this.plusOneDelays = new Array();
        this.fbLikeDelays = new Array();
        this.twitterDelays = new Array();
        this.lastWindowWidth = 0;
        this.lastWindowHeight = 0;
        this.lboxFullHeight = 0;
        this.thumbsBelowHeight = 0;

        if( this.chk( document.id(this.options.largeImage) ) )
        {
            document.id(this.options.largeImage).setStyle('height', this.options.largestHeight);
            document.id(this.options.largeImage).set('html', '');
            document.id(this.options.largeImage).setStyle('visibility','visible');
        }
		
		if(this.options.main == 1)
		{
			this.initializeMain();
		}
		
		if(this.options.showThumbs == 1 && this.options.main == 1)
		{
			this.initializeThumbs(this.imageIndex, 0);
		}
		
		if( (this.options.showSlideshowControls == 1 || this.options.slideshowAutostart == 1)  && this.options.main == 1 && this.options.showLargeImage == 1)
		{
			this.initializeSlideShow();
		}

        if(this.options.showLargeImage == 0 && this.options.lightboxOn == 1 && this.options.calledFrom != 'module')
        {
            if( window.location.hash.indexOf('!') != -1)
            {
                var hashVar = window.location.hash;
                var fileNameClean = hashVar.substr(2);

                this.options.jsonImages.general.each(function(el,index)
                {
                    var fileNameShort = el.filename.substring(0, el.filename.indexOf('-'))

                    if( fileNameShort == fileNameClean )
                    {
                        this.showLightBox.delay(750, this, index);
                    }
                }.bind(this));
            }
        }
    },

	initializeMain: function()
	{
        if(this.options.showLargeImage == 1 && this.options.refreshMode == 'hash')
        {
            if(window.location.hash.length == 0)
            {
                this.addHash(this.options.jsonImagesImageType[0]);
            }
        }

        var urlImage = this.getUrlParamater('image');

		if(urlImage != 'unset' && this.options.calledFrom != 'module')
		{
			if(urlImage != parseInt(urlImage))
			{
				for(var i=0; i<this.options.jsonImagesImageType.length; i++)
				{
					var fullPath = this.options.jsonImagesImageType[i].filename;
					var origImageName = fullPath.substring(fullPath.indexOf('/') + 1, fullPath.indexOf('/') + 1 + urlImage.length);
					if(origImageName == urlImage)
					{
						this.imageIndex = i;
						break;
					}
				}
			}
			else
			{
				this.imageIndex = urlImage - 1;
			}
		}

        this.checkHash(false);

		if(this.options.refreshMode == 'hash' && this.options.showLargeImage == 1)
		{
            this.setCheckHashPeriodical();
		}
		
		if(this.options.preload == 1 && this.options.showLargeImage == 1)
		{
			this.preloadCounter = 0;
			this.preloaderVar = this.preloadImages.periodical(750, this);
		}
		
		if(this.options.showLargeImage == 1)
		{
			this.swapImage(this.options.jsonImagesImageType[this.imageIndex], 50, this.imageIndex, false, true);
		}
		
		this.boundAddKeyEvent = this.addKeyEvent.bind(this);
		if(this.options.showLargeImage == 1)
		{
			document.addEvent('keydown', this.boundAddKeyEvent);
		}

        this.setThumbContainerHeightPeriodical = this.setThumbContainerHeight.periodical(500, this);

        if(this.options.desPostion == 'left' || this.options.desPostion == 'right' && this.options.showLargeImage == 1)
        {
            this.setDesContainerHeightPeriodical = this.setDesContainerHeight.periodical(500, this);
        }

        if(this.options.showLargeImage == 1)
		{
			this.setMainImageDivHeightPeriodical = this.setMainImageDivHeight.periodical(500, this);
		}

		if(this.options.allowRating == 2)
        {
		    this.initializeRating();
        }

	},

	initializeRating: function()
    {
        if( this.chk( document.id(this.options.ratingsContainer) ) )
        {
            var stars = document.id(this.options.ratingsContainer).getElements('a[class=rating_star]');

            stars.each(function(el,index)
            {
                el.addEvent('click', function(event)
                {
                    event.stop();
                    document.id(this.options.ratingsContainer).getElement('span[class=rating_loading_gif]').setStyle('visibility', 'visible');

                    var ratingUrl = 'index.php?option=com_igallery&task=image.addRating&format=raw&rating=' + (index + 1) + '&imageid=' + this.options.idArray[this.imageIndex];

                    var ratingAjax = new Request({url: ratingUrl, method: 'get',

                    onFailure: function(response)
                    {
                        document.id(this.options.ratingsContainer).getElement('span[class=rating_message]').set('html','Error: ' + response.statusText);
                    },

                    onSuccess: function(response)
                    {
                        document.id(this.options.ratingsContainer).getElement('span[class=rating_loading_gif]').setStyle('visibility', 'hidden');
                        console.log(response);
                        response = response.trim();
                        if( response.indexOf('{') > 0 )
                        {
                            response  = response.substr(response.indexOf('{') );
                        }

                        responseObject = JSON.decode(response);

                        document.id(this.options.ratingsContainer).getElement('span[class=rating_message]').set('html',responseObject.message);
                        if(responseObject.success == 1)
                        {
                            this.options.jsonImages.general[this.imageIndex].ratingAverage = responseObject.average;
                            this.options.jsonImages.general[this.imageIndex].ratingCount++;

                            this.swapRatings(this.imageIndex, false);
                        }
                    }.bind(this)});

                    ratingAjax.send();
                }.bind(this));
            }.bind(this));
        }
    },

    setThumbContainerHeight: function()
    {
        if(this.options.thumbPostion == 'above' || this.options.thumbPostion == 'below')
        {
            if( this.chk( document.id(this.options.thumbContainer) ) )
            {
                if(document.id(this.options.thumbContainer).getStyle('max-height') == 'none')
                {
                    document.id(this.options.thumbContainer).setStyle('height', 'auto');
                    var currentHeight = document.id(this.options.thumbContainer).getSize().y;
                    if(currentHeight != this.thumbsBelowHeight)
                    {
                        var newHeight = currentHeight + 4;
                        document.id(this.options.thumbContainer).setStyle('height', newHeight);
                        this.thumbsBelowHeight = newHeight;
                    }
                }
            }
        }
        else
        {
            if(this.options.showLargeImage == 1)
            {
                var largeImgDivSize = document.id(this.options.largeImage).getSize();

                if(largeImgDivSize.y > 50)
                {
                    if( this.chk( document.id(this.options.thumbContainer) ) )
                    {
                        document.id(this.options.thumbContainer).setStyle('max-height', largeImgDivSize.y);
                    }
                }
            }
        }
    },

    setMainImageDivHeight: function()
    {
        var largestHeight = 0
		var imageHolders = document.id(this.options.largeImage).getElements('div[class=large_img_holder]');

		imageHolders.each(function(el,index)
		{
			if(el.getStyle('display') != 'none')
			{
				var elementHeight = el.getSize().y;
				largestHeight = elementHeight > largestHeight ? elementHeight : largestHeight;
			}
		});

		if(largestHeight > 50)
        {
            if( this.chk( document.id(this.options.largeImage) ) )
            {
                document.id(this.options.largeImage).setStyle('height', largestHeight);
            }
        }
    },

	setLboxImageDivHeight: function()
    {
        var largestHeight = 0
		var imageHolders = document.id(this.lboxGalleryObject.options.largeImage).getElements('div[class=large_img_holder]');

		imageHolders.each(function(el,index)
		{
			if(el.getStyle('display') != 'none')
			{
				var elementHeight = el.getSize().y;
				largestHeight = elementHeight > largestHeight ? elementHeight : largestHeight;
			}
		});

		if(largestHeight > 50)
        {
            if( this.chk( document.id(this.lboxGalleryObject.options.largeImage) ) )
            {
                document.id(this.lboxGalleryObject.options.largeImage).setStyle('height', largestHeight);
            }
        }
    },

    setDesContainerHeight: function()
    {
        var largeImgDivSize = document.id(this.options.largeImage).getSize();

        if(largeImgDivSize.y > 50)
        {
            if( this.chk( document.id(this.options.desContainer) ) )
            {
                document.id(this.options.desContainer).setStyle('max-height', largeImgDivSize.y);
            }
        }
    },

    lboxSetThumbContainerHeight: function()
    {
        var largeImgDivSize = document.id(this.lboxGalleryObject.options.largeImage).getSize();

        if(largeImgDivSize.y > 50)
        {
            if( this.chk( document.id(this.lboxGalleryObject.options.thumbContainer) ) )
            {
                document.id(this.lboxGalleryObject.options.thumbContainer).setStyle('max-height', largeImgDivSize.y);
            }
        }
    },

    lboxSetDesContainerHeight: function()
    {
        var largeImgDivSize = document.id(this.lboxGalleryObject.options.largeImage).getSize();

        if(largeImgDivSize.y > 50)
        {
            if( this.chk( document.id(this.lboxGalleryObject.options.desContainer) ) )
            {
                document.id(this.lboxGalleryObject.options.desContainer).setStyle('max-height', largeImgDivSize.y);
            }
        }
    },
	
	addKeyEvent: function(event)
	{
		if(event.key == 'right')
		{
			this.clearSlideShow();
			this.slideShowSwap(true);
		}
		if(event.key == 'left')
		{
			this.clearSlideShow();
			this.slideShowSwap(false);
		}
	},
	
	initializeThumbs: function(index, lightboxShowCounter)
	{
        var activeThumbId = this.options.prefix + '-' + this.options.uniqueid + '-' + (index + 1);
        if( this.chk( document.id(activeThumbId) ) )
        {
            var ScrolltoThumb = new Fx.Scroll(this.options.thumbContainer,{duration: 50});
            ScrolltoThumb.toElement( document.id(activeThumbId) );
        }

        if(lightboxShowCounter == 0)
        {
            var thumbScroller = new Scroller(this.options.thumbContainer, {area: this.options.scrollBoundary, velocity: this.options.scrollSpeed});
            document.id(this.options.thumbTable).addEvent('mouseenter', thumbScroller.start.bind(thumbScroller));
            document.id(this.options.thumbTable).addEvent('mouseleave', thumbScroller.stop.bind(thumbScroller));

            if(this.options.showUpDown == 1)
            {
                this.addArrowBehaviors(this.options.upArrow, -200, 'vertical');
                this.addArrowBehaviors(this.options.downArrow, 200, 'vertical');
            }

            if(this.options.showLeftRight == 1)
            {
                this.addArrowBehaviors(this.options.rightArrow, 200, 'horizontal');
                this.addArrowBehaviors(this.options.leftArrow, -200, 'horizontal');
            }

            var thumblinksArray = document.id(this.options.thumbTable).getElements('a[class=imglink]');

            thumblinksArray.each(function(el,index)
            {
                el.addEvent('click', function(event)
                {
                    event.stop();
                    this.clearSlideShow();

                    if(this.options.showLargeImage == 0 && this.options.main == 1)
                    {
                        var imgLink = this.options.jsonImages.general[index].url;
                        var imgTargetBlank = this.options.jsonImages.general[index].targetBlank;

                        if(imgLink.length > 1)
                        {
                            el.setStyle('cursor', 'pointer');

                            if(imgTargetBlank == 1)
                            {
                                window.open(imgLink);
                            }
                            else
                            {
                                window.location = imgLink;
                            }
                        }
                        else
                        {
                            this.showLightBox(index);
                        }
                    }
                    else
                    {
                        this.swapImage(this.options.jsonImagesImageType[index], this.options.fadeDuration, index, true, false);
                    }

                }.bind(this));

            }.bind(this));
        }
	},
	
	initializeSlideShow: function()
	{
		this.clearSlideShow();
		
		
		if(this.options.showSlideshowControls == 1)
		{
			document.id(this.options.slideshowForward).removeEvents();
			document.id(this.options.slideshowForward).addEvent('click', function(e)
			{
				this.clearSlideShow();
				this.slideShowSwap(true);
			}.bind(this));
			
			document.id(this.options.slideshowRewind).removeEvents();
			document.id(this.options.slideshowRewind).addEvent('click', function(e)
			{
				this.clearSlideShow();
				this.slideShowSwap(false);
			}.bind(this));
		}
		
		if(this.options.slideshowAutostart == 1 && this.options.showLargeImage == 1)
		{
			this.slideShowStart(false);
		}
	},
	
	checkHash: function(refreshImage)
	{
        var hashVar = window.location.hash;

        if(hashVar != this.currentHash && hashVar.length > 0)
        {
            var fileNameClean = hashVar.substr(2);

            if( fileNameClean.indexOf('#') >= 0 )
            {
                fileNameClean  = fileNameClean.substr(0, fileNameClean.indexOf('#') );
            }

            this.options.jsonImages.general.each(function(el,index)
            {
                var fileNameShort = el.filename.substring(0, el.filename.indexOf('-'))

                if( fileNameShort == fileNameClean )
                {
                    this.imageIndex = index;
                }
            }.bind(this));

            if(refreshImage == true)
            {
                this.swapImage(this.options.jsonImagesImageType[this.imageIndex], this.options.fadeDuration, this.imageIndex, true, false);
            }

            this.currentHash = hashVar;
        }

	},

    setCheckHashPeriodical : function()
    {
        this.checkHashPeriodical = this.checkHash.periodical(400, this, true);
    },

    clearCheckHashPeriodical : function()
    {
        clearInterval(this.checkHashPeriodical);
    },
    
	addHash : function(imageObject)
	{
		var slashPos = imageObject.filename.indexOf('/');
		var fileNameOnly  = imageObject.filename.substr(slashPos + 1);
		
		var dashPos = fileNameOnly.indexOf('-');
		var fileNameClean  = fileNameOnly.substr(0, dashPos);
		var hashToAdd = '!' + fileNameClean;
		
		window.location.hash = hashToAdd;
		this.currentHash = '#' + hashToAdd;
	},
	
	getUrlParamater : function (paramTarget)
	{
		var urlValue = 'unset';
		var url = window.location.href;
		
		if(url.indexOf("?") > -1)
		{
			var queryParams = url.substr(url.indexOf("?"));
			var queryParamsArray = queryParams.split("&");
			
			for(var i=0; i< queryParamsArray.length; i++ )
			{
				if( queryParamsArray[i].indexOf(paramTarget + "=") > -1 )
				{
					var paramMatch = queryParamsArray[i].split("=");
					urlValue = paramMatch[1];
					break;
				}
			}
		}
		return unescape(urlValue);
	},
	
	preloadImages : function()
	{
		new Asset.images([this.options.resizePath + this.options.jsonImagesImageType[this.preloadCounter].filename ],
		{
			onComplete: function(){}
		});
		
		this.preloadCounter++;
		
		if(this.preloadCounter == this.options.numPics)
		{
            clearInterval(this.preloaderVar);
		}
	},
	
	lboxPreloadStarter : function()
	{
		this.preloadCounter = 0;
		this.preloaderVar = this.preloadImages.periodical(750, this);
	},
	
	addArrowBehaviors: function(arrow, pixels, mode)
	{
		var arrowScroller = new Fx.Scroll(this.options.thumbContainer);
		
		document.id(arrow).addEvent('click', function(e)
		{
			var containerSizeArray = document.id(this.options.thumbContainer).getScroll();
			var currentScrollX = containerSizeArray.x;
			var currentScrollY = containerSizeArray.y;
			
			if(mode == 'horizontal')
			{
				arrowScroller.start(currentScrollX + pixels, currentScrollY);
			}
			
			if(mode == 'vertical')
			{
				arrowScroller.start(currentScrollX, currentScrollY + pixels);
			}
		}.bind(this));
	},
	
	slideShowStart : function(instant)
	{
		if(instant == true)
		{
			this.slideShowSwap(true);
		}

		this.slideShowObject = this.slideShowSwap.periodical(this.options.slideshowPause, this, true);
		
		if(this.options.showSlideshowControls == 1 && this.chk( document.id(this.options.slideshowPlay) )  )
		{
			document.id(this.options.slideshowPlay).setProperty('src', this.options.imageAssetPath + 'pause.jpg');
			document.id(this.options.slideshowPlay).removeEvents();
			document.id(this.options.slideshowPlay).addEvent('click', function(e)
			{
				this.clearSlideShow();
			}.bind(this));
		}
	},
	
	slideShowSwap : function(forward)
	{
		if(forward == true)
		{
			if(this.imageIndex == this.options.numPics - 1)
			{
				this.imageIndex = 0;
			}
			else
			{
				this.imageIndex++;
			}
		}
		else
		{
			if(this.imageIndex == 0)
			{
				this.imageIndex = this.options.numPics - 1;
			}
			else
			{
				this.imageIndex--;
			}
		}
		
		this.swapImage(this.options.jsonImagesImageType[this.imageIndex], this.options.fadeDuration, this.imageIndex, true, false );
	},

	clearSlideShow : function()
	{
        clearInterval(this.slideShowObject);
	
		if(this.options.showSlideshowControls == 1 && this.chk( document.id(this.options.slideshowPlay) ) )
		{
			document.id(this.options.slideshowPlay).setProperty('src', this.options.imageAssetPath + 'play.jpg');
			document.id(this.options.slideshowPlay).removeEvents();
			document.id(this.options.slideshowPlay).addEvent('click', function(e)
			{
				this.slideShowStart(true);
			}.bind(this));
		}
	},
	
	swapImage : function(imageObject, fadeDuration, index, addHash, firstShow)
	{
		this.imageIndex = index;
		
		this.insertImage(imageObject, fadeDuration, this.imageIndex);
		
		if(this.options.main == 1 && this.options.showLargeImage == 1)
		{
			this.addMainImageClick(this.imageIndex);
		}
		
		if(this.options.showThumbs == 1)
		{
			this.swapThumbsClass(index);
		}
		
		if(this.options.showDescriptions == 1)
		{
			this.swapDescription(index);
		}
		
		if(this.options.showTags == 1)
		{
			this.swapTags(index);
		}
		
		if(this.options.downloadType != 'none')
		{
			this.swapDownload(index);
		}
		
		if(this.options.facebookShare == 1)
		{
			this.swapFacebook(index, imageObject);
		}
		
		if(this.options.showPlusOne == 1)
		{
			this.swapPlusOne(index, imageObject);
		}

        if(this.options.twitterButton == 1)
        {
            this.swapTwitterButton(index, imageObject);
        }

        if(this.options.allowComments == 2)
		{
			this.swapJcomments(index, imageObject);
		}
		
		if(this.options.allowComments == 4)
		{
			this.swapFbComments(index, imageObject);
		}
		
		if(this.options.reportImage == 1)
		{
			this.swapReportImage(index);
		}
		
		if(this.options.numberingOn == 1)
		{
			this.swapNumbering(index);
		}
		
		if(this.options.allowRating == 2)
		{
			this.swapRatings(index, true);
		}

        if(this.options.refreshMode == 'hash' && addHash == true)
        {
            this.addHash(imageObject);
        }
		
		if(this.options.collectImageViews == 1)
		{
			this.addImageHit();
		}

        this.imageShowCounter++;
	},
	
	insertImage : function(imageObject, fadeDuration, index)
	{
		if(this.lastImageDisplayedIndex == index)
		{
			return;
		}
		
		if(this.lastImageDisplayedIndex >= 0)
		{
			var imageToRemove = this.lastImageDisplayed;
			var imageToRemoveWidth = parseInt(imageToRemove.getStyle('width'));
			var imageToRemoveHeight = parseInt(imageToRemove.getStyle('height'));
		}
		else
		{
			var imageToRemove = null;
			var imageToRemoveWidth = 0;
			var imageToRemoveHeight = 0;
		}
		
		var insertedImageHolders = document.id(this.options.largeImage).getElements('div[class=large_img_holder]');
		var insertedMatch = false;

		for(var i=0; i<insertedImageHolders.length; i++)
		{
			var insertedImageHolderId = insertedImageHolders[i].getProperty('id');
			var idSplitted = insertedImageHolderId.split('-');
			var insertedImageHolderCounter = idSplitted[2];

			if(insertedImageHolderCounter == index)
			{
				insertedMatch = true;
				holderToInsert = document.id(insertedImageHolderId);
				break;
			}
		}

		if(insertedMatch == true)
		{
            var imageToInsert = document.id(insertedImageHolderId).getElement('img');

			var imageToInsertWidth = imageToInsert.getStyle('width').toInt();
			var imageToInsertHeight = imageToInsert.getStyle('height').toInt();

			var widthDiff = imageToInsertWidth - imageToRemoveWidth;
			var heightDiff = imageToInsertHeight - imageToRemoveHeight;

			if( widthDiff < 5 && widthDiff > -5 && heightDiff < 5 && heightDiff > -5)
			{
				var wait = true;
			}
			else
			{
				var wait = false;
				this.removeImage(fadeDuration, insertedImageHolderId);
			}

			document.id(insertedImageHolderId).setStyle('z-index', this.zIndex);
			document.id(insertedImageHolderId).setStyle('display', 'block');
			this.zIndex++;

            var imageFadeIn = new Fx.Tween(insertedImageHolderId, {property:'opacity', duration:fadeDuration}).start(0,1).chain(function()
			{
				if(wait == true)
				{
					this.removeImage(50, insertedImageHolderId);
				}
			}.bind(this));

            this.lastImageDisplayed = imageToInsert;
            this.lastImageDisplayedIndex = index;

			if(this.options.main == true && this.options.magnify == 1 && this.options.lightboxOn == 1)
			{
				this.insertMagnify(index, imageObject);
			}
		}

		else
		{
			if( (Math.abs(parseInt(imageObject.width - imageToRemoveWidth)) < 5) && (Math.abs(parseInt(imageObject.height - imageToRemoveHeight)) < 5) )
			{
				var wait = true;
			}
			else
			{
				var wait = false;
				this.removeImage(fadeDuration, 'ig' + this.options.main + '-' + this.options.uniqueid + '-' + index);
			}

			var ImageAsset = new Asset.images([this.options.resizePath + imageObject.filename ],
			{
				onComplete: function()
				{
                    ImageAsset.removeProperty('width');
                    ImageAsset.removeProperty('height');

                    var insertDimensions = this.getInsertImageDimensions(imageObject);

					ImageAsset.setStyles
					({
                        'max-width': insertDimensions.imageToInjectWidthPercent + '%',
                        'margin-left': insertDimensions.MarginLeft + '%',
                        'margin-right': insertDimensions.MarginRight + '%',
                        'margin-top': insertDimensions.MarginTop + '%',
                        'margin-bottom': insertDimensions.MarginBottom + '%',
                        padding: insertDimensions.imageToInjectPaddingPercent + '%'
                        /*'max-width': '100%'imageObject.width*/
					});

					ImageAsset.setProperty('class', 'large_img');
					ImageAsset.setProperty('alt', this.options.jsonImages.general[index].alt);

                    var imageHolderId = 'ig' + this.options.main + '-' + this.options.uniqueid + '-' + index;
                    var largeImgHolder = new Element('div', {'id': imageHolderId, 'class': 'large_img_holder', styles: {position: 'absolute', width: '100%', 'z-index': this.zIndex, opacity: 0} });
					largeImgHolder.inject( document.id(this.options.largeImage), 'top' );
					this.zIndex++;
                    ImageAsset.inject( document.id(imageHolderId) );
                    this.setMainImageDivHeight();
					document.id(this.options.largeImage).setStyle('visibility','visible');

					var ImageHolderInjected = document.id(imageHolderId);
					var imageFadeIn = new Fx.Tween(ImageHolderInjected, {property:'opacity',duration:fadeDuration}).start(0,1).chain(function()
					{
						if(wait == true)
						{
							this.removeImage(50, imageHolderId);
						}
					}.bind(this));

                    this.lastImageDisplayed = document.id(imageHolderId).getElement('img');
                    this.lastImageDisplayedIndex = index;

					if(this.options.main == true && this.options.magnify == 1 && this.options.lightboxOn == 1)
					{
						this.insertMagnify(index, imageObject);
					}

                }.bind(this)
			});
		}
	},

    getInsertImageDimensions: function(imageObject)
    {
        var imageToInjectMarginBasePercent = (Math.round( (this.options.largeImageMargin/this.options.largeImageDivWidth) * 10000) /10000) * 100;
        var imageToInjectPaddingPercent = (Math.round( (this.options.largeImagePadding/this.options.largeImageDivWidth) * 10000) /10000) * 100;
        var insidePaddingPercent = 100 - ( (imageToInjectMarginBasePercent * 2) + (imageToInjectPaddingPercent * 2) );

        var imageToInjectWidthPercent = (Math.round( (imageObject.width/this.options.largeImageDivWidth) * 10000) /10000) * 100;
        var horizontalSpacingMarginPercent = (insidePaddingPercent - imageToInjectWidthPercent) / 2;

        var verticalMarginSpacingPixel = ( (this.options.largestHeight - imageObject.height) / 2);
        var verticalMarginSpacingPercent = (Math.round( (verticalMarginSpacingPixel/this.options.largeImageDivWidth) * 10000) /10000) * 100;

        if(this.options.imageAlignHoriz == 'left')
        {
            var MarginLeft = imageToInjectMarginBasePercent;
            var MarginRight = imageToInjectMarginBasePercent + (horizontalSpacingMarginPercent * 2);

        }
        else if(this.options.imageAlignHoriz == 'center')
        {
            var MarginLeft = imageToInjectMarginBasePercent + horizontalSpacingMarginPercent;
            var MarginRight = imageToInjectMarginBasePercent + horizontalSpacingMarginPercent;
        }
        else if(this.options.imageAlignHoriz == 'right')
        {
            var MarginLeft = imageToInjectMarginBasePercent + (horizontalSpacingMarginPercent * 2);
            var MarginRight = imageToInjectMarginBasePercent;
        }

        if(this.options.imageAlignVert == 'top')
        {
            var MarginTop = imageToInjectMarginBasePercent;
            var MarginBottom = imageToInjectMarginBasePercent + (verticalMarginSpacingPercent * 2);

        }
        else if(this.options.imageAlignVert == 'center')
        {
            var MarginTop = imageToInjectMarginBasePercent + verticalMarginSpacingPercent;
            var MarginBottom = imageToInjectMarginBasePercent + verticalMarginSpacingPercent;

        }
        else if(this.options.imageAlignVert == 'bottom')
        {
            var MarginTop = imageToInjectMarginBasePercent + (verticalMarginSpacingPercent * 2);
            var MarginBottom = imageToInjectMarginBasePercent;
        }

        insertDimensions = new Object();
        insertDimensions.imageToInjectWidthPercent = imageToInjectWidthPercent;
        insertDimensions.imageToInjectPaddingPercent = imageToInjectPaddingPercent;
        insertDimensions.MarginLeft = MarginLeft;
        insertDimensions.MarginRight = MarginRight;
        insertDimensions.MarginTop = MarginTop;
        insertDimensions.MarginBottom = MarginBottom;

        return insertDimensions;
    },
  
	removeImage : function(fadeDuration, currentImgHolderId)
	{
		var insertedImageHolders = document.id(this.options.largeImage).getElements('div[class=large_img_holder]');

		for(var i=0; i<insertedImageHolders.length; i++)
	    {
			var opacity = insertedImageHolders[i].getStyle('opacity');

			if(opacity != 0)
			{
				if(insertedImageHolders[i].getProperty('id') != currentImgHolderId)
				{
					this.imageFadeAway = new Fx.Tween(insertedImageHolders[i], {property:'opacity',duration:fadeDuration}).start(0).chain(function()
                    {
                        this.element.setStyle('display', 'none');
                    });
				}
			}
	    }

		if(this.options.main == 1 && this.options.lightboxOn == 1)
		{
			if (this.options.magnify == 1 && this.chk( document.id('magnifygif' + this.options.uniqueid) ) )
			{
				document.id('magnifygif' + this.options.uniqueid).dispose();
			}
		}
	},
	
	insertMagnify : function(index, imageObject)
	{
		var magnifyImage = new Asset.images([this.options.imageAssetPath + 'magnify.gif' ],
		{
			onComplete: function()
			{
                var insertDimensions = this.getInsertImageDimensions(imageObject);

                var magnifyWidthPercent = (Math.round( (magnifyImage[0].width/this.options.largeImageDivWidth) * 10000) /10000) * 100;
                var magnifyHeightPercent = (Math.round( (magnifyImage[0].height/this.options.largeImageDivWidth) * 10000) /10000) * 100;

                if(Browser.safari)
                {
                    var ImageHeightPercent = (Math.round( (imageObject.height/(this.options.largestHeight + (this.options.largeImageMargin * 2) + (this.options.largeImagePadding * 2) )) * 10000) /10000) * 100;
                }
                else
                {
                    var ImageHeightPercent = (Math.round( (imageObject.height/this.options.largeImageDivWidth) * 10000) /10000) * 100;
                }

                var magnifyLeftPercent = insertDimensions.imageToInjectWidthPercent + insertDimensions.imageToInjectPaddingPercent + insertDimensions.MarginLeft - magnifyWidthPercent;
                var magnifyTopPercent = ImageHeightPercent + insertDimensions.imageToInjectPaddingPercent + insertDimensions.MarginTop - magnifyHeightPercent;

                magnifyImage[0].inject(this.options.largeImage).setStyles
				({
					position: 'absolute',
					left: 0,
					top: 0,
                    'margin-left': (magnifyLeftPercent - 0.5) + '%',
                    'margin-top': (magnifyTopPercent - 0.5) + '%',
					'z-index': 100,
					opacity: 0.7,
                    width: magnifyWidthPercent + '%',
                    height: 'auto'
				});
				
				magnifyImage[0].setProperty('id', 'magnifygif' + this.options.uniqueid);
			
			}.bind(this)
		});
	},
	
	addMainImageClick : function(index)
	{
		var imgLink = this.options.jsonImages.general[index].url;
		var imgTargetBlank = this.options.jsonImages.general[index].targetBlank;
		
		if(imgLink.length > 1)
		{
			document.id(this.options.largeImage).setStyle('cursor', 'pointer');
			document.id(this.options.largeImage).removeEvents('click');
			
			document.id(this.options.largeImage).addEvent('click', function(e)
			{
				if (imgTargetBlank == 1)
				{
					window.open(imgLink);
				}
				else
				{
					window.location = imgLink;
				}
			}.bind(this));
		}
		
		if(this.options.lightboxOn == 1 && imgLink.length < 2 )
		{
			document.id(this.options.largeImage).removeEvents('click');
			
			document.id(this.options.largeImage).setStyle('cursor', 'pointer');
			
			document.id(this.options.largeImage).addEvent('click', function(e)
			{
				this.showLightBox(index);
			}.bind(this));
		}
		
		else if(this.options.lightboxOn == 0 && imgLink.length < 2 )
		{
			document.id(this.options.largeImage).removeEvents('click');
			
			document.id(this.options.largeImage).addEvent('click', function(e)
			{
				this.clearSlideShow();
				this.slideShowSwap(true);
			}.bind(this));
		}
	},
	
	swapThumbsClass : function(index)
	{
		var thumbCells = document.id(this.options.thumbTable).getElements('td');
		thumbCells.each(function(el,index)
		{
			el.setProperty('class','inactive_thumb');
		}.bind(this));
		thumbCells[index].setProperty('class','active_thumb');
	},
	
	swapDescription : function(index)
	{
		if( this.chk( document.id(this.options.desContainer) ) )
		{
			var descriptionDivs = document.id(this.options.desContainer).getElements('div[class=des_div]');
			descriptionDivs.each(function(el,index)
			{
				el.setStyle('display', 'none');
			});
			
			document.id(this.options.desContainer).scrollTo(0,0);
			descriptionDivs[index].setStyle('display', 'block');
		}
	},
  
	swapTags : function(index)
	{
		var tagsDivs = document.id(this.options.tagsContainer).getElements('div[class=tags_div]');
		
		tagsDivs.each(function(el,index)
		{
			el.setStyle('display', 'none');
		});
		
		tagsDivs[index].setStyle('display', 'block');
		
		var taglinks = tagsDivs[index].getElements('a');
		
		if(taglinks.length == 0)
		{
			tagsDivs[index].setStyle('visibility', 'hidden');
		}
	},

    swapFacebook : function(index, imageObject)
    {
        if(this.options.facebookLegacy == 1)
        {
            var urlToShare = this.getLegacyUrl(index, imageObject);
        }
        else
        {
            var urlToShare = this.getHashUrl(index, imageObject);
        }

        var fbContainer = document.id(this.options.facebookContainer);
        var fbTempContainer = document.id(this.options.facebookTempContainer);
        var fbHtml = '<fb:like send="false" layout="button_count" show_faces="true" href="' + urlToShare + '" width="' + this.options.fbLikeWidth + '" colorscheme="' + this.options.facebookColor + '" ></fb:like>';

        fbTempContainer.set('html',fbHtml);
        if(typeof(FB) != 'undefined')
        {
            FB.XFBML.parse(fbTempContainer);
        }

        var fbLikeToMove = fbTempContainer.getFirst();
        fbLikeToMove.setStyles
        ({
            position: 'absolute',
            top: 0,
            left: 0
        });
        fbContainer.grab(fbLikeToMove, 'top');

        this.fbLikeDelays.each(function(item, index, array)
        {
            clearTimeout(item);
            array.erase(item);
        });


        if(this.imageShowCounter > 0)
        {
            this.fbLikeDelays[index] = this.removeFBLike.delay(1500, this);
        }
    },

    removeFBLike : function()
    {
        var fbLikeTags = document.id(this.options.facebookContainer).getChildren();

        fbLikeTags.each(function(el,index)
        {
            if(index != 0)
            {
                el.dispose();
            }

        }.bind(this));
    },

    swapTwitterButton : function(index, imageObject)
    {
        var urlToShare = this.getHashUrl(index, imageObject);

        var twitterButton = new Element('a', {'href': 'https://twitter.com/share', 'class': 'twitter-share-button', 'data-url': urlToShare/*, 'data-text': urlToShare*/});
        twitterButton.setStyles
        ({
            position: 'absolute',
            top: 0,
            left: 0
        });
        twitterButton.inject(this.options.twitterButtonDiv, 'top');

        if(typeof(twttr) != 'undefined')
        {
            twttr.widgets.load();
        }

        this.twitterDelays.each(function(item, index, array)
        {
            clearTimeout(item);
            array.erase(item);
        });

        if(this.imageShowCounter > 0)
        {
            this.twitterDelays[index] = this.removeTwitter.delay(1500, this);
        }
    },

    removeTwitter : function()
    {
        var twitterButtons = document.id(this.options.twitterButtonDiv).getChildren();

        twitterButtons.each(function(el,index)
        {
            if(index != 0)
            {
                el.dispose();
            }

        }.bind(this));
    },

    swapPlusOne : function(index, imageObject)
    {
        if(this.options.facebookLegacy == 1)
        {
            var urlToShare = this.getLegacyUrl(index, imageObject);
        }
        else
        {
            var urlToShare = this.getHashUrl(index, imageObject);
        }

        var buttonId = 'plus_one_button_' + this.options.prefix + index;
        var loadingPlusOne = new Element('div', {'id': buttonId, 'class': 'plus_one_button_inj'});
        loadingPlusOne.inject(this.options.plusOneDiv, 'top');

        if(typeof(gapi) != 'undefined')
        {
            gapi.plusone.render(buttonId,{"href": urlToShare, "size": "medium"});
        }

        this.plusOneDelays.each(function(item, index, array)
        {
            clearTimeout(item);
            array.erase(item);
        });

        if(this.imageShowCounter > 0)
        {
            this.plusOneDelays[index] = this.removePlusOne.delay(1500, this);
        }
    },

    removePlusOne : function()
    {
        var plusOneDivs = document.id(this.options.plusOneDiv).getElements('div[class=plus_one_button_inj]');

        plusOneDivs.each(function(el,index)
        {
            if(index != 0)
            {
                el.dispose();
            }

        }.bind(this));
    },

    getLegacyUrl : function(index, imageObject)
    {
        var currentUrl = window.location.href;
        if(currentUrl.indexOf("#") > -1)
        {
            currentUrl = currentUrl.substr(0, currentUrl.indexOf("#"));
        }

        if(currentUrl.indexOf("&fb_comment_id") > -1)
        {
            currentUrl = currentUrl.substr(0, currentUrl.indexOf("&fb_comment_id"));
        }

        if(currentUrl.indexOf("?_escaped_fragment_") > -1)
        {
            currentUrl = currentUrl.substr(0, currentUrl.indexOf("?_escaped_fragment_"));
        }

        var urlSymbol = currentUrl.indexOf("?") > -1 ? '&' : '?';
        var filepathArray = imageObject.filename.split('/');
        var fullFilename = filepathArray[1];
        var filenameArray = fullFilename.split('-');
        var OrigFilename =  filenameArray[0] + '-' +  filenameArray[1];
        var legacyUrl = currentUrl + urlSymbol + 'image=' + OrigFilename;

        return legacyUrl;
    },

    getHashUrl : function(index, imageObject)
    {
        var currentUrl = window.location.href;
        if(currentUrl.indexOf("#") > -1)
        {
            currentUrl = currentUrl.substr(0, currentUrl.indexOf("#"));
        }

        if(currentUrl.indexOf("&fb_comment_id") > -1)
        {
            currentUrl = currentUrl.substr(0, currentUrl.indexOf("&fb_comment_id"));
        }

        if(currentUrl.indexOf("?_escaped_fragment_") > -1)
        {
            currentUrl = currentUrl.substr(0, currentUrl.indexOf("?_escaped_fragment_"));
        }

        var slashPos = imageObject.filename.indexOf('/');
        var fileNameOnly  = imageObject.filename.substr(slashPos + 1);
        var dashPos = fileNameOnly.indexOf('-');
        var fileNameClean  = fileNameOnly.substr(0, dashPos);
        var hashToAdd = '#!' + fileNameClean;
        var hashUrl = currentUrl + hashToAdd;

        return hashUrl;
    },
	
	swapJcomments : function(index, imageObject)
	{
		if(typeof(jcomments) != 'undefined')
		{
			jcomments.showPage(this.options.idArray[index],'com_igallery',0);
			
			if(! this.chk( document.id('comments-form') ) )
			{
				if( this.chk( document.id('addcomments') ) )
				{
					var addCommentLink = document.id('addcomments');
					addCommentLink.setProperty('onclick', "jcomments.showForm(" + this.options.idArray[index] + ",'com_igallery\', 'comments-form-link'); return false;");
				}
			}
			else
			{
				var objectIdInput = document.id('comments-form').getElement('input[name=object_id]');
				objectIdInput.setProperty('value', this.options.idArray[index]);
				
				var jcforms = document.id('jc').getElements('form[id=comments-form]');
				jcforms.each(function(el,index)
				{
					if(index != 0)
					{
						el.dispose();
					}
				});
			}
		}
	},
	
	swapFbComments : function(index, imageObject)
	{
        if(this.options.facebookLegacy == 1)
        {
            var urlToShare = this.getLegacyUrl(index, imageObject);
        }
        else
        {
            var urlToShare = this.getHashUrl(index, imageObject);
        }
		
		var fbContainer = document.id(this.options.facebookCommentsContainer);
		var fbWidth = fbContainer.getStyle('width').toInt();
		
		var fbHtml = '<fb:comments href="' + urlToShare + '" num_posts="' + this.options.facebookCommentsNumPosts + '" width="' + fbWidth + '" colorscheme="' + this.options.facebookColor + '"></fb:comments>';
		fbContainer.set('html',fbHtml);

        if(typeof(FB) != 'undefined')
        {
		    FB.XFBML.parse(fbContainer);
        }
		
	},
	
	resetLboxHeight : function()
	{
		var totalScrollHeight = Window.getScrollHeight();
		document.id(this.options.lboxDark).setStyle('height',totalScrollHeight);
	},
	
	
	swapReportImage : function(index)
	{
		var reportForm = document.id(this.options.reportContainer).getElement('form');
		reportForm.setStyle('display', 'none');
		
		var reportUrl = this.options.host + 'index.php?option=com_igallery&task=image.reportImage&id=' + this.options.idArray[index] + '&catid=' + this.options.catid;
		reportForm.setProperty('action', reportUrl);
		
		var reportLink = document.id(this.options.reportContainer).getElement('a');
		reportLink.removeEvents();
		reportLink.addEvent('click', function(event)
		{
            event.stop();
			reportForm.setStyle('display', 'block');
		}.bind(this));
	},
	
	swapNumbering : function(index)
	{
		if( this.chk( document.id(this.options.numberingContainer) ) )
		{
			document.id(this.options.numberingContainer).getElement('span').set('html', index + 1);
		}
	},
	
	swapDownload : function(index)
	{
		if( this.chk( document.id(this.options.downloadId) ) )
		{
			var downloadLink = document.id(this.options.downloadId).getElement('a');
			var linkType = this.options.main == 1 ? 'main' : 'lbox';
			var url = this.options.host + 'index.php?option=com_igallery&task=image.download&format=raw&type=' + linkType + '&id=' + this.options.idArray[index];
			downloadLink.setProperty('href',url);
		}
	},
	
	swapRatings : function(index, hideMessage)
	{
        if( this.chk(document.id(this.options.ratingsContainer)) )
        {
            if(hideMessage == true)
            {
                document.id(this.options.ratingsContainer).getElement('span[class=rating_message]').set('html','');
            }

            document.id(this.options.ratingsContainer).getElement('span[class=rating_loading_gif]').setStyle('visibility', 'hidden');
            var width = this.options.jsonImages.general[index].ratingAverage * 20;

            document.id(this.options.ratingsContainer).getElement('div[class=ratings_current]').setStyle('width', width + '%');

            document.id(this.options.ratingsContainer).getElement('span[class=rating_number]').set('html',this.options.jsonImages.general[index].ratingAverage);

            document.id(this.options.ratingsContainer).getElement('span[class=rating_count]').set('html',this.options.jsonImages.general[index].ratingCount);

            if(this.options.jsonImages.general[index].ratingCount == 1)
            {
                document.id(this.options.ratingsContainer).getElement('span[class=rating_vote_vote]').setStyle('display', 'inline');
                document.id(this.options.ratingsContainer).getElement('span[class=rating_vote_votes]').setStyle('display', 'none');
            }
            else
            {
                document.id(this.options.ratingsContainer).getElement('span[class=rating_vote_vote]').setStyle('display', 'none');
                document.id(this.options.ratingsContainer).getElement('span[class=rating_vote_votes]').setStyle('display', 'inline');
            }
        }
	},
	
	addImageHit : function()
	{
		var hitUrl = 'index.php?option=com_igallery&task=image.addHit&format=raw&id=' + this.options.idArray[this.imageIndex];
		var hitAjax = new Request({url: hitUrl, method: 'get', 
		onComplete: function(response){}.bind(this)});
		    
		hitAjax.send();
	},

    setLboxWidth: function()
    {
        var windowWidth = Window.getWidth();
        var windowHeight = window.getSize().y;

        if(windowWidth != this.lastWindowWidth || windowHeight != this.lastWindowHeight)
        {
            var lboxPaddingLeft = document.id(this.options.lboxWhite).getStyle('padding-left').toInt();
            var lboxPaddingRight = document.id(this.options.lboxWhite).getStyle('padding-right').toInt();
            var lboxWidthNoPadding = this.options.lightboxWidth;
            var lboxWidthWithPadding = this.options.lightboxWidth + lboxPaddingLeft + lboxPaddingRight;
            var lboxMargin = 30;
            var windowHeightSpace = windowHeight - (lboxMargin * 2);

            if(this.lboxFullHeight == 0)
            {
                this.lboxFullHeight = document.id(this.lboxGalleryObject.options.imageSlideshowContainer).measure(function()
                {
                    return this.getSize().y + 30;
                });

                if(this.lboxGalleryObject.options.thumbPostion == 'above' || this.lboxGalleryObject.options.thumbPostion == 'below')
                {
                    if( this.chk( document.id(this.lboxGalleryObject.options.thumbContainer ) ) )
                    {
                        this.lboxFullHeight =  this.lboxFullHeight + 40 + document.id(this.lboxGalleryObject.options.thumbContainer).measure(function()
                        {
                            return this.getSize().y;
                        });
                    }
                }
            }

            if(lboxWidthWithPadding < (windowWidth - (lboxMargin * 2) ) )
            {
                var whiteDivWidth = lboxWidthNoPadding;
                var whiteDivLeftMargin = (windowWidth - lboxWidthWithPadding) / 2;
            }
            else
            {
                var whiteDivWidth = windowWidth - ( (lboxMargin * 2) + lboxPaddingLeft + lboxPaddingRight);
                var whiteDivLeftMargin = lboxMargin;
            }

            if(this.lboxFullHeight > windowHeightSpace )
            {
                var ratio = windowHeightSpace/this.lboxFullHeight;

                if(lboxWidthNoPadding * ratio < whiteDivWidth)
                {
                    whiteDivWidth = lboxWidthNoPadding * ratio;
                    if(whiteDivWidth < (windowWidth - (lboxMargin * 2) ) )
                    {
                        var whiteDivLeftMargin = (windowWidth - (whiteDivWidth + lboxPaddingLeft + lboxPaddingRight)) / 2;
                    }
                    else
                    {
                        var whiteDivLeftMargin = lboxMargin;
                    }
                }
            }

            document.id(this.options.lboxWhite).setStyles
            ({
                'width': whiteDivWidth,
                'margin-left': whiteDivLeftMargin
            });

            this.lastWindowWidth = windowWidth;
            this.lastWindowHeight = windowHeight;
        }
    },
  
	showLightBox : function(index)
	{
        this.lboxGalleryObject.imageIndex = index;
        this.lboxGalleryObject.checkHash(false);

        this.clearSlideShow();

        if(this.options.refreshMode == 'hash')
        {
            this.lboxGalleryObject.setCheckHashPeriodical();
            this.clearCheckHashPeriodical();
        }

		if(this.lboxGalleryObject.options.preload == 1 && this.lboxPreloadStarted == false)
		{
			this.lboxGalleryObject.lboxPreloadStarter();
			this.lboxPreloadStarted = true;
		}
		
		var bodyTag = document.getElementsByTagName("body").item(0);
		var scrolledDown = Window.getScrollTop();
		var totalScrollHeight = Window.getScrollHeight();
		var windowWidth = Window.getWidth();
		var windowHeight = Window.getHeight();
		var lboxPaddingLeft = document.id(this.options.lboxWhite).getStyle('padding-left').toInt();
		var lboxPaddingRight = document.id(this.options.lboxWhite).getStyle('padding-right').toInt();
		var lboxPadding = (lboxPaddingLeft + lboxPaddingRight) / 2;

        this.setLboxWidth();
        this.setLboxWidthPeriodical = this.setLboxWidth.periodical(600, this);

		if(this.lightboxShowCounter == 0)
		{
			document.id(this.options.lboxWhite).inject(bodyTag, 'top');
		}

		document.id(this.options.lboxWhite).setStyles
		({
			'top': scrolledDown + 30,
			'opacity': '0',
			'display': 'block'
		});
		
		totalScrollHeight = Window.getScrollHeight();
		
		document.id(this.options.lboxDark).inject(bodyTag, 'top');
		document.id(this.options.lboxDark).setStyles
		({
            'height': totalScrollHeight,
            'opacity': '0',
			'display': 'block'
		});

        this.resetLboxHeightPeriodical = this.resetLboxHeight.periodical(750, this);


        if(this.lboxGalleryObject.options.thumbPostion == 'above' || this.lboxGalleryObject.options.thumbPostion == 'below')
        {

        }
        else
        {
            this.lboxSetThumbContainerHeightPeriodical = this.lboxSetThumbContainerHeight.periodical(500, this);
        }

        if(this.lboxGalleryObject.options.desPostion == 'left' || this.lboxGalleryObject.options.desPostion == 'right')
        {
            this.lboxSetDesContainerHeightPeriodical = this.lboxSetDesContainerHeight.periodical(500, this);
        }
		
		var darkDivFade = new Fx.Tween(document.id(this.options.lboxDark), {property:'opacity', duration: 1000});
		darkDivFade.start(0,.7);
		
		var whiteDivFadeIn = new Fx.Tween(document.id(this.options.lboxWhite), {property:'opacity', duration: 1000});
		whiteDivFadeIn.start(0,1);
		
		if(this.lboxGalleryObject.options.allowComments == 2 && this.options.allowComments == 2)
		{
			var jcommentsClone = document.id('jc').clone(true, true);
			document.id('jc').dispose();
			
			jcommentsClone.inject(this.options.jCommentsLbox);
			this.totalScrollHeight = Window.getScrollHeight();
			document.id(this.options.lboxDark).setStyle('height', this.totalScrollHeight);
		}
		
		this.lboxGalleryObject.swapImage(this.options.jsonImages.lbox[this.lboxGalleryObject.imageIndex], 0, this.lboxGalleryObject.imageIndex, true, false);
		
		if(this.lboxGalleryObject.options.allowComments == 2 && this.options.allowComments == 2)
		{
			var jcforms = document.id('jc').getElements('form[id=comments-form]');
			jcforms.each(function(el,index)
			{
				if(index != 0)
				{
					el.dispose();
				}
			});
		}
		
		this.lboxGalleryObject.initializeSlideShow();

		if(this.lboxGalleryObject.options.allowRating == 2 && this.lightboxShowCounter == 0)
        {
		    this.lboxGalleryObject.initializeRating();
        }
		
		document.id(this.lboxGalleryObject.options.largeImage).removeEvents();
		document.id(this.lboxGalleryObject.options.largeImage).addEvent('click', function(e)
		{
			this.lboxGalleryObject.clearSlideShow();
			this.lboxGalleryObject.slideShowSwap(true);
		}.bind(this));
		
		if(this.lboxGalleryObject.options.showThumbs == 1)
		{
			this.lboxGalleryObject.initializeThumbs(this.lboxGalleryObject.imageIndex, this.lightboxShowCounter);
		}

		this.setLboxImageDivHeightPeriodical = this.setLboxImageDivHeight.periodical(500, this);
		
		document.removeEvent('keydown', this.boundAddKeyEvent);
		this.lboxBoundAddKeyEvent = this.lboxGalleryObject.addKeyEvent.bind(this.lboxGalleryObject);
		document.addEvent('keydown', this.lboxBoundAddKeyEvent);
		
		if(this.chk(document.id(this.lboxGalleryObject.options.closeImage)))
		{
			document.id(this.lboxGalleryObject.options.closeImage).removeEvents();
			
			document.id(this.lboxGalleryObject.options.closeImage).addEvent('click', function(e)
			{
				this.closeLightBox(this.lboxGalleryObject.imageIndex);
			}.bind(this));
		}
		
		document.id(this.options.lboxDark).removeEvents();
		document.id(this.options.lboxDark).addEvent('click', function(e)
		{
			this.closeLightBox(this.lboxGalleryObject.imageIndex);
		}.bind(this));
		
		this.lightboxShowCounter++;

        if(this.options.showLargeImage == 0 && this.options.refreshMode == 'hash')
        {
            if(window.location.hash.length == 0)
            {
                this.addHash(this.options.jsonImagesImageType[this.lboxGalleryObject.imageIndex]);
            }
        }
		
	},
	
	closeLightBox : function(index)
	{
        if(this.options.refreshMode == 'hash')
        {
            this.lboxGalleryObject.clearCheckHashPeriodical();

            if(this.options.showLargeImage == 1)
            {
                this.setCheckHashPeriodical();
            }

        }

		this.lboxGalleryObject.clearSlideShow();
		this.lboxGalleryObject.lastImageDisplayedIndex = -1;
		
		if(this.lboxGalleryObject.options.allowComments == 2 && this.options.allowComments == 2)
		{
			var jcommentsClone = document.id('jc').clone(true, true);
			document.id('jc').dispose();
			jcommentsClone.inject(this.options.jCommentsMain);
			
			jcomments.showPage(this.options.idArray[this.imageIndex],'com_igallery',0);
			
			if(! this.chk( document.id('comments-form') ) )
			{
				if( this.chk( document.id('addcomments') ) )
				{
					var addCommentLink = document.id('addcomments');
					addCommentLink.setProperty('onclick', "jcomments.showForm(" + this.options.idArray[this.imageIndex] + ",'com_igallery\', 'comments-form-link'); return false;");
				}
			}
			else
			{
				var objectIdInput = document.id('comments-form').getElement('input[name=object_id]');
				objectIdInput.setProperty('value', this.options.idArray[this.imageIndex]);
				
				var jcforms = document.id('jc').getElements('form[id=comments-form]');
				jcforms.each(function(el,index)
				{
					if(index != 0)
					{
						el.dispose();
					}
				});
			}
		}
		
		var darkDivFade = new Fx.Tween(document.id(this.options.lboxDark), {property:'opacity', duration: 100});
		darkDivFade.start(0.7,0).chain(function()
		{
			document.id(this.options.lboxDark).setStyle('display','none');
		}.bind(this));
		
		var whiteDivFadeIn = new Fx.Tween(document.id(this.options.lboxWhite), {property:'opacity', duration: 100});
		whiteDivFadeIn.start(1,0).chain(function()
		{
			document.id(this.lboxGalleryObject.options.largeImage).set('html','');
			document.id(this.options.lboxWhite).setStyle('display','none');
		}.bind(this));
		
		document.removeEvent('keydown', this.lboxBoundAddKeyEvent);
		if(this.options.showLargeImage == 1)
		{
			this.boundAddKeyEvent = this.addKeyEvent.bind(this);
			document.addEvent('keydown', this.boundAddKeyEvent);

            this.swapImage(this.options.jsonImagesImageType[this.lboxGalleryObject.imageIndex], 50, this.lboxGalleryObject.imageIndex, true, false);
		}
	},

    chk : function(obj)
    {
        return !!(obj || obj === 0);
    }
};
 
 