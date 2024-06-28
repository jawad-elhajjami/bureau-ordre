function initializePdfViewer() {
    const container = document.getElementById('pdf-viewer-container');
    const prevPageBtn = document.getElementById('prev-page');
    const nextPageBtn = document.getElementById('next-page');
    const zoomInBtn = document.getElementById('zoom-in');
    const zoomOutBtn = document.getElementById('zoom-out');
    const fitToViewBtn = document.getElementById('fit-to-view');
    const rotatePageBtn = document.getElementById('rotate-page');
    const pageInput = document.getElementById('page-input');
    const pageCountSpan = document.getElementById('page-count');

    let currentPage = 1;
    let scale = 1;
    let rotation = 0; // Rotation angle in degrees
    let pdfDocument = null;
    const minScale = 0.5; // Minimum scale value
    const maxScale = 2.5; // Maximum scale value

    const loadingTask = pdfjsLib.getDocument(url);
    loadingTask.promise.then(pdf => {
        pdfDocument = pdf;
        pageCountSpan.textContent = pdf.numPages;
        renderPage(currentPage);

        prevPageBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderPage(currentPage);
            }
        });

        nextPageBtn.addEventListener('click', () => {
            if (currentPage < pdf.numPages) {
                currentPage++;
                renderPage(currentPage);
            }
        });

        zoomInBtn.addEventListener('click', () => {
            if (scale < maxScale) {
                scale += 0.1;
                renderPage(currentPage);
            }
        });

        zoomOutBtn.addEventListener('click', () => {
            if (scale > minScale) {
                scale -= 0.1;
                renderPage(currentPage);
            }
        });

        fitToViewBtn.addEventListener('click', () => {
            fitToView();
        });

        rotatePageBtn.addEventListener('click', () => {
            rotatePage();
        });

        pageInput.addEventListener('change', () => {
            let pageNumber = parseInt(pageInput.value);
            if (pageNumber >= 1 && pageNumber <= pdf.numPages) {
                currentPage = pageNumber;
                renderPage(currentPage);
            } else {
                pageInput.value = currentPage;
            }
        });
    }, function (reason) {
        console.error(reason);
    });

    function renderPage(pageNum) {
        pdfDocument.getPage(pageNum).then(page => {
            const viewport = page.getViewport({ scale: scale, rotation: rotation });

            // Prepare canvas using PDF page dimensions
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            // Clear previous canvas content
            container.innerHTML = '';

            // Append canvas to the container
            container.appendChild(canvas);

            // Render PDF page into canvas context
            const renderContext = {
                canvasContext: context,
                viewport: viewport
            };
            page.render(renderContext);

            // Update the input field value
            pageInput.value = pageNum;
        });
    }

    function fitToView() {
        pdfDocument.getPage(currentPage).then(page => {
            const viewport = page.getViewport({ scale: 1, rotation: rotation });
            const containerWidth = container.clientWidth;
            const containerHeight = container.clientHeight;

            const widthScale = containerWidth / viewport.width;
            const heightScale = containerHeight / viewport.height;

            scale = Math.min(widthScale, heightScale);
            renderPage(currentPage);
        });
    }

    function rotatePage() {
        rotation = (rotation + 90) % 360; // Rotate by 90 degrees
        renderPage(currentPage);
    }
}

document.addEventListener('DOMContentLoaded', initializePdfViewer);