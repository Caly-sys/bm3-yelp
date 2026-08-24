import * as PIXI from 'pixi.js';

export async function initPixiHero() {
    const canvas = document.getElementById('pixiCanvas');
    if (!canvas) return;

    // Respect reduced motion preference
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    try {
        const heroSection = document.getElementById('heroSection') || canvas.parentElement;
        const width = heroSection.clientWidth || window.innerWidth;
        const height = heroSection.clientHeight || 450;

        const app = new PIXI.Application();
        
        await app.init({
            canvas: canvas,
            width: width,
            height: height,
            backgroundAlpha: 0,
            antialias: true,
            resolution: window.devicePixelRatio || 1,
            autoDensity: true,
        });

        const container = new PIXI.Container();
        app.stage.addChild(container);

        // Particle configuration
        const particleCount = Math.min(45, Math.floor(width / 25));
        const particles = [];

        // Colors palette (Pixiv iconic blue, cyan, coral pink, ranking gold, lavender)
        const colors = [0x0096FA, 0x00C8FF, 0xFF4060, 0xFFAA00, 0x8B5CF6];

        for (let i = 0; i < particleCount; i++) {
            const graphics = new PIXI.Graphics();
            const radius = Math.random() * 4.5 + 2;
            const color = colors[Math.floor(Math.random() * colors.length)];
            const alpha = Math.random() * 0.4 + 0.15;

            // Draw soft circle particle
            graphics.circle(0, 0, radius);
            graphics.fill({ color: color, alpha: alpha });

            // Optional glowing outer ring for some particles
            if (Math.random() > 0.6) {
                graphics.circle(0, 0, radius * 2.2);
                graphics.stroke({ width: 1, color: color, alpha: alpha * 0.4 });
            }

            graphics.x = Math.random() * width;
            graphics.y = Math.random() * height;

            const particle = {
                graphics: graphics,
                vx: (Math.random() - 0.5) * 0.45,
                vy: (Math.random() - 0.5) * 0.45,
                baseAlpha: alpha,
                pulseSpeed: Math.random() * 0.02 + 0.01,
                pulseOffset: Math.random() * Math.PI * 2,
                radius: radius,
            };

            container.addChild(graphics);
            particles.push(particle);
        }

        // Mouse interaction
        let mouseX = -1000;
        let mouseY = -1000;

        heroSection.addEventListener('mousemove', (e) => {
            const rect = canvas.getBoundingClientRect();
            mouseX = e.clientX - rect.left;
            mouseY = e.clientY - rect.top;
        });

        heroSection.addEventListener('mouseleave', () => {
            mouseX = -1000;
            mouseY = -1000;
        });

        let tick = 0;

        // Animation loop
        app.ticker.add((time) => {
            tick += 0.03;

            for (let i = 0; i < particles.length; i++) {
                const p = particles[i];

                p.graphics.x += p.vx;
                p.graphics.y += p.vy;

                // Wrap around edges
                if (p.graphics.x < -20) p.graphics.x = width + 20;
                if (p.graphics.x > width + 20) p.graphics.x = -20;
                if (p.graphics.y < -20) p.graphics.y = height + 20;
                if (p.graphics.y > height + 20) p.graphics.y = -20;

                // Pulse alpha gently
                p.graphics.alpha = p.baseAlpha + Math.sin(tick + p.pulseOffset) * 0.12;

                // Mouse repulsion/interaction
                const dx = p.graphics.x - mouseX;
                const dy = p.graphics.y - mouseY;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < 120 && dist > 0) {
                    const force = (120 - dist) / 120;
                    p.graphics.x += (dx / dist) * force * 1.5;
                    p.graphics.y += (dy / dist) * force * 1.5;
                }
            }
        });

        // Resize handler with debounce
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                const newWidth = heroSection.clientWidth || window.innerWidth;
                const newHeight = heroSection.clientHeight || 450;
                app.renderer.resize(newWidth, newHeight);
            }, 200);
        });

        // Pause ticker when tab is not visible
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                app.stop();
            } else {
                app.start();
            }
        });

    } catch (err) {
        console.warn('PixiJS background initialization skipped:', err);
    }
}
