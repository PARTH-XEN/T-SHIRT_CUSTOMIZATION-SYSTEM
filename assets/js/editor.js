document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('tshirt-canvas');
    if (!canvas) return; // Only run on editor page
    const ctx = canvas.getContext('2d');
    const container = document.getElementById('editor-container');
    
    // Anti-Gravity 3D tilt effect
    container.addEventListener('mousemove', (e) => {
        const rect = container.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        
        const rotateX = ((y - centerY) / centerY) * -8;
        const rotateY = ((x - centerX) / centerX) * 8;
        
        canvas.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
    });
    
    container.addEventListener('mouseleave', () => {
        canvas.style.transform = `rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)`;
    });

    // Custom Canvas State
    let elements = [];
    let selectedElement = null;
    let isDragging = false;
    let dragOffsetX = 0;
    let dragOffsetY = 0;
    
    // Inertia setup
    let velocityX = 0;
    let velocityY = 0;
    let lastX = 0;
    let lastY = 0;
    let animationFrameId = null;

    let bgImage = new Image();
    bgImage.src = FRONT_IMG;
    bgImage.onload = draw;
    bgImage.onerror = () => { bgImage = null; draw(); };

    let isFront = true;

    document.getElementById('btn-front').addEventListener('click', () => {
        isFront = true;
        bgImage.src = FRONT_IMG;
        document.getElementById('btn-front').classList.add('btn-accent');
        document.getElementById('btn-front').style.backgroundColor = '';
        document.getElementById('btn-front').style.color = '';
        
        document.getElementById('btn-back').classList.remove('btn-accent');
        document.getElementById('btn-back').style.backgroundColor = '#ddd';
        document.getElementById('btn-back').style.color = '#333';
        draw();
    });

    document.getElementById('btn-back').addEventListener('click', () => {
        isFront = false;
        bgImage.src = BACK_IMG;
        document.getElementById('btn-back').classList.add('btn-accent');
        document.getElementById('btn-back').style.backgroundColor = '';
        document.getElementById('btn-back').style.color = '';

        document.getElementById('btn-front').classList.remove('btn-accent');
        document.getElementById('btn-front').style.backgroundColor = '#ddd';
        document.getElementById('btn-front').style.color = '#333';
        draw();
    });

    // Color Palette Listener
    const colorSwatches = document.querySelectorAll('.color-swatch');
    const customColorInput = document.getElementById('custom-shirt-color');

    if (colorSwatches && customColorInput) {
        colorSwatches.forEach(swatch => {
            swatch.addEventListener('click', function() {
                colorSwatches.forEach(s => s.classList.remove('active'));
                this.classList.add('active');
                defaultShirtColor = this.dataset.color;
                if(document.getElementById('selected-color-input')) {
                    document.getElementById('selected-color-input').value = defaultShirtColor;
                }
                draw();
            });
        });

        customColorInput.addEventListener('input', function() {
            colorSwatches.forEach(s => s.classList.remove('active'));
            defaultShirtColor = this.value;
            if(document.getElementById('selected-color-input')) {
                document.getElementById('selected-color-input').value = defaultShirtColor;
            }
            draw();
        });
    }

    // Image Upload
    document.getElementById('upload-img').addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (f) => {
            const img = new Image();
            img.onload = () => {
                elements.push({
                    type: 'image',
                    img: img,
                    x: canvas.width/2 - 50,
                    y: canvas.height/2 - 50,
                    w: 100,
                    h: 100 * (img.height/img.width),
                    view: isFront ? 'front' : 'back',
                    floatingY: 0,
                    floatDir: 1,
                });
                draw();
            };
            img.src = f.target.result;
        };
        reader.readAsDataURL(file);
    });

    // Add Text
    document.getElementById('btn-add-text').addEventListener('click', () => {
        const text = document.getElementById('custom-text').value;
        const color = document.getElementById('text-color').value;
        const size = document.getElementById('text-size').value;
        if(text.trim() === '') return;
        
        ctx.font = `${size}px Arial`;
        const metrics = ctx.measureText(text);

        elements.push({
            type: 'text',
            text: text,
            color: color,
            size: parseInt(size),
            x: canvas.width/2 - metrics.width/2,
            y: canvas.height/2,
            w: metrics.width,
            h: parseInt(size),
            view: isFront ? 'front' : 'back',
            floatingY: 0,
            floatDir: 1,
        });
        document.getElementById('custom-text').value = '';
        draw();
    });

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        if (bgImage) {
            const hRatio = canvas.width / bgImage.width;
            const vRatio = canvas.height / bgImage.height;
            const ratio  = Math.min ( hRatio, vRatio );
            const centerShift_x = ( canvas.width - bgImage.width*ratio ) / 2;
            const centerShift_y = ( canvas.height - bgImage.height*ratio ) / 2;  
            const dsx = centerShift_x;
            const dsy = centerShift_y;
            const dsw = bgImage.width*ratio;
            const dsh = bgImage.height*ratio;

            if (defaultShirtColor && defaultShirtColor.toLowerCase() !== '#ffffff') {
                // 1. Draw solid color
                ctx.globalCompositeOperation = 'source-over';
                ctx.fillStyle = defaultShirtColor;
                ctx.fillRect(dsx, dsy, dsw, dsh);

                // 2. Multiply image on top
                ctx.globalCompositeOperation = 'multiply';
                ctx.drawImage(bgImage, dsx, dsy, dsw, dsh);

                // 3. Mask out transparency
                ctx.globalCompositeOperation = 'destination-in';
                ctx.drawImage(bgImage, dsx, dsy, dsw, dsh);
                
                ctx.globalCompositeOperation = 'source-over'; // reset
            } else {
                ctx.globalCompositeOperation = 'source-over';
                ctx.drawImage(bgImage, dsx, dsy, dsw, dsh);
            }
        } else {
            ctx.fillStyle = '#f5f5f5';
            ctx.fillRect(0,0, canvas.width, canvas.height);
            ctx.fillStyle = '#ccc';
            ctx.font = '20px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('No Base Image Available', canvas.width/2, canvas.height/2);
            ctx.textAlign = 'left';
        }

        const currentView = isFront ? 'front' : 'back';

        elements.forEach(el => {
            if (el.view !== currentView) return;
            
            ctx.save();
            ctx.translate(el.x, el.y + el.floatingY);
            
            if (el.type === 'image') {
                ctx.drawImage(el.img, 0, 0, el.w, el.h);
                if (el === selectedElement) drawSelection(el.w, el.h);
            } else if (el.type === 'text') {
                ctx.font = `${el.size}px Arial`;
                ctx.fillStyle = el.color;
                ctx.textBaseline = 'top';
                ctx.fillText(el.text, 0, 0);
                if (el === selectedElement) {
                    ctx.font = `${el.size}px Arial`;
                    const w = ctx.measureText(el.text).width;
                    el.w = w; // update width for clicking later
                    drawSelection(w, el.h);
                }
            }
            ctx.restore();
        });
    }

    function drawSelection(w, h) {
        ctx.strokeStyle = '#3498db';
        ctx.lineWidth = 1.5;
        ctx.setLineDash([5, 5]);
        ctx.strokeRect(-4, -4, w+8, h+8);
        ctx.setLineDash([]);
        
        // Handles
        ctx.fillStyle = '#3498db';
        ctx.fillRect(-8, -8, 8, 8); // TL
        ctx.fillRect(w, -8, 8, 8); // TR
        ctx.fillRect(-8, h, 8, 8); // BL
        ctx.fillRect(w, h, 8, 8); // BR
    }

    // Interaction loop
    canvas.addEventListener('mousedown', (e) => {
        cancelAnimationFrame(animationFrameId); // stop inertia
        const rect = canvas.getBoundingClientRect();
        // Adjust for scale3d transform
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        const mx = (e.clientX - rect.left) * scaleX;
        const my = (e.clientY - rect.top) * scaleY;
        
        selectedElement = null;
        const currentView = isFront ? 'front' : 'back';
        
        for (let i = elements.length - 1; i >= 0; i--) {
            const el = elements[i];
            if (el.view !== currentView) continue;
            
            if (mx >= el.x && mx <= el.x + el.w &&
                my >= el.y + el.floatingY && my <= el.y + el.floatingY + el.h) {
                selectedElement = el;
                isDragging = true;
                dragOffsetX = mx - el.x;
                dragOffsetY = my - (el.y + el.floatingY);
                lastX = mx;
                lastY = my;
                
                // bring to front
                elements.push(elements.splice(i, 1)[0]);
                break;
            }
        }
        draw();
    });

    canvas.addEventListener('mousemove', (e) => {
        if (!isDragging || !selectedElement) return;
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        const mx = (e.clientX - rect.left) * scaleX;
        const my = (e.clientY - rect.top) * scaleY;
        
        velocityX = mx - lastX;
        velocityY = my - lastY;
        lastX = mx;
        lastY = my;

        selectedElement.x = mx - dragOffsetX;
        selectedElement.y = my - dragOffsetY - selectedElement.floatingY;
        draw();
    });

    canvas.addEventListener('mouseup', () => {
        isDragging = false;
        if (selectedElement) {
            applyInertia();
        }
    });

    canvas.addEventListener('mouseleave', () => {
        isDragging = false;
    });

    // Delete selected element
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Delete' || e.key === 'Backspace') {
            if (selectedElement && document.activeElement.tagName !== 'INPUT') {
                elements = elements.filter(el => el !== selectedElement);
                selectedElement = null;
                draw();
            }
        }
    });

    function applyInertia() {
        if (!selectedElement) return;
        
        if (Math.abs(velocityX) > 0.5 || Math.abs(velocityY) > 0.5) {
            selectedElement.x += velocityX;
            selectedElement.y += velocityY;
            
            velocityX *= 0.9;
            velocityY *= 0.9;
            
            draw();
            animationFrameId = requestAnimationFrame(applyInertia);
        }
    }

    // Anti-gravity floating effect animation
    setInterval(() => {
        if(isDragging) return;
        let requiresRedraw = false;
        elements.forEach(el => {
            el.floatingY += 0.2 * el.floatDir;
            if(el.floatingY > 3) el.floatDir = -1;
            if(el.floatingY < -3) el.floatDir = 1;
            requiresRedraw = true;
        });
        if(requiresRedraw) draw();
    }, 50);

    // Save Design
    document.getElementById('btn-save-design').addEventListener('click', () => {
        // deselect before saving to hide bounding box
        selectedElement = null;
        draw();
        
        const saveBtn = document.getElementById('btn-save-design');
        saveBtn.innerText = 'Saving...';
        
        const designData = JSON.stringify(elements.map(el => {
            if(el.type === 'image') {
                return { ...el, img: el.img.src };
            }
            return el;
        }));
        
        // Export just the design part without background for better storage, or with background for preview
        const previewImage = canvas.toDataURL('image/png');

        fetch('api/save_design.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                product_id: PRODUCT_ID,
                design_data: designData,
                preview: previewImage
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('design-id-input').value = data.design_id;
                document.getElementById('btn-add-cart').disabled = false;
                document.getElementById('save-warning').style.display = 'none';
                saveBtn.innerText = 'Saved \u2713';
                saveBtn.style.backgroundColor = '#218838';
                setTimeout(() => {
                    saveBtn.innerText = 'Save Design';
                }, 2000);
            } else {
                alert('Error saving design: ' + data.error);
                saveBtn.innerText = 'Save Design';
            }
        })
        .catch(err => {
            console.error(err);
            saveBtn.innerText = 'Save Design';
        });
    });

});
