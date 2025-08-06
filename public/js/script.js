document.addEventListener('DOMContentLoaded', () => {
    // --- DOM Elements ---
    const designArea = document.getElementById('design-area');
    const colorFill = document.getElementById('color-fill');
    const motifArea = document.getElementById('motif-area');
    const garmentOutline = document.getElementById('garment-outline');
    const svg = document.getElementById('garment-svg');
    const motifContainer = document.getElementById('motif-container');
    const controlsContainer = document.getElementById('controls-container');
    const selectionBox = controlsContainer.querySelector('.selection-box');
    const resizeHandle = document.getElementById('resize-handle');
    const rotateHandle = document.getElementById('rotate-handle');
    const deleteHandle = document.getElementById('delete-handle');

    const motifItems = document.querySelectorAll('.motif-item');
    const shirtBtn = document.getElementById('shirtBtn');
    const dressBtn = document.getElementById('dressBtn');
    const clothColorInput = document.getElementById('clothColor');
    const resetBtn = document.getElementById('resetBtn');
    const downloadBtn = document.getElementById('downloadBtn');
    const uploadBtn = document.getElementById('uploadBtn');
    const customMotifInput = document.getElementById('customMotifInput');
    const defaultMotifsContainer = document.getElementById('default-motifs-container');
    const customMotifThumbnailContainer = document.getElementById('custom-motif-thumbnail-container');

    // --- State ---
    const initialState = {
        maskUrl: '',
        outlineUrl: '',
        solidColor: '#FFFFFF',
        motifs: [], // Store motif data for rendering and download
    };
    let designState = JSON.parse(JSON.stringify(initialState));

    let selectedElement = null;
    let currentAction = null; // 'drag', 'resize', 'rotate'
    let offset, transform;

    // --- SVG Point Conversion ---
    function getSVGPoint(x, y) {
        const pt = svg.createSVGPoint();
        pt.x = x;
        pt.y = y;
        return pt.matrixTransform(svg.getScreenCTM().inverse());
    }

    // --- Selection ---
    function selectElement(element) {
        if (selectedElement) deselectElement();
        selectedElement = element;
        selectedElement.classList.add('selected');
        updateControls();
        controlsContainer.style.display = 'block';
    }

    function deselectElement() {
        if (!selectedElement) return;
        selectedElement.classList.remove('selected');
        selectedElement = null;
        controlsContainer.style.display = 'none';
    }

    function updateControls() {
        if (!selectedElement) return;

        const bbox = selectedElement.getBBox();
        const transformAttr = selectedElement.getAttribute('transform') || '';

        selectionBox.setAttribute('x', bbox.x);
        selectionBox.setAttribute('y', bbox.y);
        selectionBox.setAttribute('width', bbox.width);
        selectionBox.setAttribute('height', bbox.height);
        
        resizeHandle.setAttribute('cx', bbox.x + bbox.width);
        resizeHandle.setAttribute('cy', bbox.y + bbox.height);
        
        rotateHandle.setAttribute('cx', bbox.x + bbox.width / 2);
        rotateHandle.setAttribute('cy', bbox.y - 20);

        deleteHandle.setAttribute('transform', `translate(${bbox.x + bbox.width -5}, ${bbox.y + 5})`);

        controlsContainer.setAttribute('transform', transformAttr);
    }

    // --- Motif Management ---
    function addMotif(imageUrl) {
        const image = document.createElementNS('http://www.w3.org/2000/svg', 'image');
        image.setAttributeNS('http://www.w3.org/1999/xlink', 'href', imageUrl);
        image.setAttribute('x', 200);
        image.setAttribute('y', 200);
        image.setAttribute('width', 100);
        image.setAttribute('height', 100);
        image.setAttribute('class', 'motif-image');
        image.setAttribute('preserveAspectRatio', 'none');
        
        motifContainer.appendChild(image);
        selectElement(image);

        image.addEventListener('mousedown', (e) => {
            e.stopPropagation();
            selectElement(image);
            startDrag(e);
        });
    }

    defaultMotifsContainer.addEventListener('click', (e) => {
        if (e.target.classList.contains('motif-item')) {
            addMotif(e.target.src);
        }
    });

    // --- Garment Toggle ---
    function setActiveGarment(garment) {
        const shirtData = shirtBtn.dataset;
        const dressData = dressBtn.dataset;

        if (garment === 'shirt') {
            shirtBtn.classList.replace('inactive-garment-btn', 'active-garment-btn');
            dressBtn.classList.replace('active-garment-btn', 'inactive-garment-btn');
            designState.maskUrl = shirtData.maskSrc;
            designState.outlineUrl = shirtData.outlineSrc;
        } else {
            dressBtn.classList.replace('inactive-garment-btn', 'active-garment-btn');
            shirtBtn.classList.replace('active-garment-btn', 'inactive-garment-btn');
            designState.maskUrl = dressData.maskSrc;
            designState.outlineUrl = dressData.outlineSrc;
        }
        renderDesign();
        resetDesign(); // Reset motifs and color when garment changes
    }
    shirtBtn.addEventListener('click', () => setActiveGarment('shirt'));
    dressBtn.addEventListener('click', () => setActiveGarment('dress'));

    // --- Color Picker ---
    clothColorInput.addEventListener('input', (e) => {
        designState.solidColor = e.target.value;
        renderDesign();
    });

    // --- Reset ---
    function resetDesign() {
        designState.solidColor = '#ffffff';
        clothColorInput.value = '#ffffff';
        motifContainer.innerHTML = ''; // Clear all motifs
        designState.motifs = []; // Clear motif data
        deselectElement();
        renderDesign(); // Re-render with cleared state
    }
    resetBtn.addEventListener('click', resetDesign);

    // --- Custom Motif Upload ---
    uploadBtn.addEventListener('click', () => customMotifInput.click());
    customMotifInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const imageUrl = e.target.result;
                const thumb = document.createElement('img');
                thumb.src = imageUrl;
                thumb.className = 'motif-item rounded-lg cursor-pointer border-2 border-transparent';
                customMotifThumbnailContainer.appendChild(thumb);
                thumb.addEventListener('click', () => addMotif(imageUrl));
            };
            reader.readAsDataURL(file);
        }
    });

    // --- Transformations ---
    function parseTransform(transformString) {
        const transforms = {};
        if (transformString) {
            transformString.match(/(\w+\s*\([^)]+\))/g)?.forEach(t => {
                const [name, value] = t.split(/\s*\(/);
                transforms[name] = value.slice(0, -1).split(/[\s,]+/).map(parseFloat);
            });
        }
        return transforms;
    }

    function applyTransform(transforms) {
        let str = '';
        if (transforms.translate) str += `translate(${transforms.translate.join(' ')}) `;
        if (transforms.rotate) str += `rotate(${transforms.rotate.join(' ')}) `;
        if (transforms.scale) str += `scale(${transforms.scale.join(' ')}) `;
        selectedElement.setAttribute('transform', str);
        controlsContainer.setAttribute('transform', str);
    }
    
    function startDrag(e) {
        currentAction = 'drag';
        const start = getSVGPoint(e.clientX, e.clientY);
        transform = parseTransform(selectedElement.getAttribute('transform'));
        const t = transform.translate || [0, 0];
        offset = { x: start.x - t[0], y: start.y - t[1] };
    }

    function startResize(e) {
        e.stopPropagation();
        currentAction = 'resize';
        const bbox = selectedElement.getBBox();
        transform = parseTransform(selectedElement.getAttribute('transform'));
        offset = {
            x: bbox.x,
            y: bbox.y,
            width: bbox.width,
            height: bbox.height
        };
    }

    function startRotate(e) {
        e.stopPropagation();
        currentAction = 'rotate';
        transform = parseTransform(selectedElement.getAttribute('transform'));
        const bbox = selectedElement.getBBox();
        const t = transform.translate || [0, 0];
        offset = {
            cx: t[0] + bbox.x + bbox.width / 2,
            cy: t[1] + bbox.y + bbox.height / 2,
        };
    }

    svg.addEventListener('mousemove', (e) => {
        if (!currentAction || !selectedElement) return;
        e.preventDefault();
        const coord = getSVGPoint(e.clientX, e.clientY);

        if (currentAction === 'drag') {
            transform.translate = [coord.x - offset.x, coord.y - offset.y];
        } else if (currentAction === 'resize') {
            const newWidth = Math.abs(coord.x - (transform.translate ? transform.translate[0] : 0) - offset.x);
            const newHeight = Math.abs(coord.y - (transform.translate ? transform.translate[1] : 0) - offset.y);
            const oldWidth = parseFloat(selectedElement.getAttribute('width'));
            const oldHeight = parseFloat(selectedElement.getAttribute('height'));
            const ratio = oldWidth / oldHeight;

            selectedElement.setAttribute('width', newWidth);
            selectedElement.setAttribute('height', newWidth / ratio);
        } else if (currentAction === 'rotate') {
            const angle = Math.atan2(coord.y - offset.cy, coord.x - offset.cx) * 180 / Math.PI + 90;
            transform.rotate = [angle, offset.cx, offset.cy];
        }
        applyTransform(transform);
        updateControls();
    });

    window.addEventListener('mouseup', () => {
        currentAction = null;
    });

    svg.addEventListener('mousedown', (e) => {
        if (e.target === svg || e.target === motifContainer) { // Clicked on SVG background or motif container
            deselectElement();
        }
    });
    
    resizeHandle.addEventListener('mousedown', startResize);
    rotateHandle.addEventListener('mousedown', startRotate);
    deleteHandle.addEventListener('mousedown', (e) => {
        e.stopPropagation();
        if (selectedElement) {
            motifContainer.removeChild(selectedElement);
            deselectElement();
        }
    });

    // --- Download ---
    downloadBtn.addEventListener('click', async () => {
        deselectElement(); // Hide controls before downloading

        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        // Set canvas dimensions to match designArea
        const designAreaRect = designArea.getBoundingClientRect();
        canvas.width = designAreaRect.width;
        canvas.height = designAreaRect.height;

        // 1. Draw solid color background
        ctx.fillStyle = designState.solidColor;
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // 2. Draw SVG content (motifs)
        const svgData = new XMLSerializer().serializeToString(svg);
        const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
        const svgUrl = URL.createObjectURL(svgBlob);
        const svgImg = new Image();
        svgImg.src = svgUrl;
        await new Promise(resolve => svgImg.onload = resolve);
        ctx.drawImage(svgImg, 0, 0, canvas.width, canvas.height);
        URL.revokeObjectURL(svgUrl);

        // 3. Apply garment mask
        const maskImg = new Image();
        maskImg.src = designState.maskUrl;
        await new Promise(resolve => maskImg.onload = resolve);

        ctx.globalCompositeOperation = 'destination-in';
        ctx.drawImage(maskImg, 0, 0, canvas.width, canvas.height);
        ctx.globalCompositeOperation = 'source-over'; // Reset to default

        // 4. Draw garment outline
        const outlineImg = new Image();
        outlineImg.src = designState.outlineUrl;
        await new Promise(resolve => outlineImg.onload = resolve);
        ctx.drawImage(outlineImg, 0, 0, canvas.width, canvas.height);

        // Trigger download
        const link = document.createElement('a');
        link.href = canvas.toDataURL('image/png');
        link.download = 'my-batik-design.png';
        link.click();
    });

    // --- Main Render Function (for garment display) ---
    function renderDesign() {
        colorFill.style.backgroundColor = designState.solidColor;
        colorFill.style.webkitMaskImage = `url("${designState.maskUrl}")`;
        colorFill.style.maskImage = `url("${designState.maskUrl}")`;
        
        motifArea.style.webkitMaskImage = `url("${designState.maskUrl}")`;
        motifArea.style.maskImage = `url("${designState.maskUrl}")`;

        garmentOutline.style.backgroundImage = `url(${designState.outlineUrl})`;
    }

    // --- Initialization ---
    function initializeState() {
        const activeGarmentBtn = document.querySelector('.garment-toggle .toggle-btn.active');
        designState = JSON.parse(JSON.stringify(initialState)); // Reset to initial state
        designState.maskUrl = activeGarmentBtn.dataset.maskSrc;
        designState.outlineUrl = activeGarmentBtn.dataset.outlineSrc;
        clothColorInput.value = designState.solidColor;
        
        motifContainer.innerHTML = ''; // Clear any existing motifs in SVG
        deselectElement(); // Deselect any active motif
        renderDesign();
    }

    initializeState();
});
