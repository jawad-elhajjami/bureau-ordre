var DWObject;
    var deviceList = [];

    function Dynamsoft_OnReady() {
        DWObject = Dynamsoft.DWT.GetWebTwain('dwtcontrolContainer');
        var token = document.querySelector("meta[name='_token']").getAttribute("content");
        DWObject.SetHTTPFormField('_token', token);

        let count = DWObject.SourceCount;
        let select = document.getElementById("source");

        DWObject.GetDevicesAsync().then(function (devices) {
            for (var i = 0; i < devices.length; i++) {
                let option = document.createElement('option');
                option.value = devices[i].displayName;
                option.text = devices[i].displayName;
                deviceList.push(devices[i]);
                select.appendChild(option);
            }
        }).catch(function (exp) {
            alert(exp.message);
        });

        updatePageInfo();
        DWObject.Viewer.autoChangeIndex = true;
        document.getElementById('DW_PreviewMode').selectedIndex = 1;

        DWObject.RegisterEvent("OnPostTransfer", function () {
            setTimeout(updatePageInfo, 20);
        });
        DWObject.RegisterEvent("OnPostLoad", function () {
            setTimeout(updatePageInfo, 20);
        });
        DWObject.Viewer.on("click", function () {
            updatePageInfo();
        });
        DWObject.Viewer.on("topPageChanged", topPageChanged);

        DWObject.Viewer.on("pageAreaSelected", function (index, rect) {
            if (rect.length > 0) {
                var currentRect = rect[rect.length - 1];
                _iLeft = currentRect.x;
                _iTop = currentRect.y;
                _iRight = currentRect.x + currentRect.width;
                _iBottom = currentRect.y + currentRect.height;
            }
        });
        DWObject.Viewer.on("pageAreaUnselected", function (index) {
            _iLeft = 0;
            _iTop = 0;
            _iRight = 0;
            _iBottom = 0;
        });

        _iLeft = 0;
        _iTop = 0;
        _iRight = 0;
        _iBottom = 0;
    }

    function topPageChanged(index) {
        updatePageInfo();
    }

    function acquireImage() {
        if (DWObject) {
            var sources = document.getElementById('source');
            if (sources) {
                DWObject.SelectDeviceAsync(deviceList[sources.selectedIndex]).then(function () {
                    return DWObject.AcquireImageAsync({
                        IfShowUI: true,
                        IfCloseSourceAfterAcquire: true
                    });
                }).catch(function (exp) {
                    alert(exp.message);
                });
            }
        }
    }

    function loadImage() {
        var OnSuccess = function () { };
        var OnFailure = function (errorCode, errorString) { };

        if (DWObject) {
            DWObject.IfShowFileDialog = true;
            DWObject.LoadImageEx("", Dynamsoft.DWT.EnumDWT_ImageType.IT_ALL, OnSuccess, OnFailure);
        }
    }

    // function upload() {
    //     var indices = [];
    //     for (var i = 0; i < DWObject.HowManyImagesInBuffer; i++) {
    //         indices.push(i);
    //     }

    //     var OnSuccess = function (httpResponse) {
    //         alert("Successfully uploaded");
    //     };

    //     var OnFailure = function (errorCode, errorString, httpResponse) {
    //         alert(httpResponse);
    //     };

    //     DWObject.HTTPUpload(
    //         "/dwt_upload/upload",
    //         indices,
    //         Dynamsoft.DWT.EnumDWT_ImageType.IT_PDF,
    //         Dynamsoft.DWT.EnumDWT_UploadDataFormat.Binary,
    //         'test.pdf',
    //         OnSuccess,
    //         OnFailure
    //     );
    // }

    document.getElementById('loadImageBtn').addEventListener('click', () => {
        loadImage();
    });

    document.getElementById('scanBtn').addEventListener('click', () => {
        acquireImage();
    });

    document.getElementById('uploadBtn').addEventListener('click', () => {
        upload();
    });

    document.getElementById('btnFirstImage').addEventListener('click', () => {
        btnFirstImage_onclick();
    });

    document.getElementById('btnPreImage').addEventListener('click', () => {
        btnPreImage_onclick();
    });

    document.getElementById('btnNextImage').addEventListener('click', () => {
        btnNextImage_onclick();
    });

    document.getElementById('btnLastImage').addEventListener('click', () => {
        btnLastImage_onclick();
    });

    document.getElementById('btnRemoveSelectedImages').addEventListener('click', () => {
        btnRemoveSelectedImages_onclick();
    });

    document.getElementById('btnRemoveAllImages').addEventListener('click', () => {
        btnRemoveAllImages_onclick();
    });

    document.getElementById('btnRotateLeft').addEventListener('click', () => {
        btnRotateLeft_onclick();
    });

    document.getElementById('btnCrop').addEventListener('click', () => {
        btnCrop_onclick();
    });

    document.getElementById('btnZoomIn').addEventListener('click', () => {
        btnZoomIn_onclick();
    });

    document.getElementById('btnZoomOut').addEventListener('click', () => {
        btnZoomOut_onclick();
    });

    document.getElementById('btnFitWindow').addEventListener('click', () => {
        btnFitWindow_onclick();
    });

    function btnFirstImage_onclick() {
        if (DWObject) {
            DWObject.Viewer.first();
            updatePageInfo();
        }
    }

    function btnPreImage_onclick() {
        if (DWObject) {
            DWObject.Viewer.off("topPageChanged", topPageChanged);
            DWObject.Viewer.previous();
            DWObject.Viewer.on("topPageChanged", topPageChanged);
            updatePageInfo();
        }
    }

    function btnNextImage_onclick() {
        if (DWObject) {
            DWObject.Viewer.off("topPageChanged", topPageChanged);
            DWObject.Viewer.next();
            DWObject.Viewer.on("topPageChanged", topPageChanged);
            updatePageInfo();
        }
    }

    function btnLastImage_onclick() {
        if (DWObject) {
            DWObject.Viewer.last();
            updatePageInfo();
        }
    }

    function btnRemoveSelectedImages_onclick() {
        if (DWObject) {
            DWObject.RemoveAllSelectedImages();
            updatePageInfo();
        }
    }

    function btnRemoveAllImages_onclick() {
        if (DWObject) {
            DWObject.RemoveAllImages();
            updatePageInfo();
        }
    }

    function btnRotateLeft_onclick() {
        if (DWObject) {
            DWObject.RotateLeft(DWObject.CurrentImageIndexInBuffer);
        }
    }

    function btnCrop_onclick() {
        if (DWObject) {
            if (!checkIfImagesInBuffer()) {
                return;
            }
            if (_iLeft != 0 || _iTop != 0 || _iRight != 0 || _iBottom != 0) {
                DWObject.Crop(
                    DWObject.CurrentImageIndexInBuffer,
                    _iLeft, _iTop, _iRight, _iBottom
                );
                _iLeft = 0;
                _iTop = 0;
                _iRight = 0;
                _iBottom = 0;
            } else {
                alert("Crop: failed. Please first select the area you'd like to crop.");
            }
        }
    }

    function checkIfImagesInBuffer() {
        if (DWObject.HowManyImagesInBuffer === 0) {
            alert("There are no images in the buffer.");
            return false;
        }
        return true;
    }


    function btnZoomIn_onclick() {
        if (DWObject) {
            if (!checkIfImagesInBuffer()) {
                return;
            }
            if(!bSingleMode){
                DWObject.Viewer.setViewMode(-1, -1);
                document.getElementById('DW_PreviewMode').selectedIndex = 0;
            }
            bSingleMode = true;

            var zoom = Math.round(DWObject.Viewer.zoom * 100);
            if (zoom >= 6500)
                return;

            var zoomInStep = 5;
            DWObject.Viewer.zoom = (zoom + zoomInStep) / 100.0;
        }
    }

    function btnZoomOut_onclick() {
        if (DWObject) {
            if (!checkIfImagesInBuffer()) {
                return;
            }
            if(!bSingleMode){
                DWObject.Viewer.setViewMode(-1, -1);
                document.getElementById('DW_PreviewMode').selectedIndex = 0;
            }
            bSingleMode = true;

            var zoom = Math.round(DWObject.Viewer.zoom * 100);
            if (zoom <= 2)
                return;

            var zoomOutStep = 5;
            DWObject.Viewer.zoom = (zoom - zoomOutStep) / 100.0;
        }
    }

    function btnFitWindow_onclick() {
        if (DWObject) {
            DWObject.Viewer.fitWindow();
        }
    }

    function setlPreviewMode() {
        if (DWObject) {
            DWObject.Viewer.setViewMode(parseInt(document.getElementById('DW_PreviewMode').value) + 1, parseInt(document.getElementById('DW_PreviewMode').value) + 1);
        }
    }

    function updatePageInfo() {
        if (DWObject) {
            document.getElementById("DW_TotalImage").value = DWObject.HowManyImagesInBuffer;
            document.getElementById("DW_CurrentImage").value = DWObject.CurrentImageIndexInBuffer + 1;
        }
    }

    function checkIfImagesInBuffer() {
        if (DWObject.HowManyImagesInBuffer === 0) {
            alert("There is no image in the buffer!");
            return false;
        }
        return true;
    }

    var bSingleMode = false;