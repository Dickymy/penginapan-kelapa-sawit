{{-- ═══════════════════════════════════════════════════════════════════════
     HERO BACKGROUND: Luxury Tropical Resort / Palm Sanctuary Theme
     Bespoke aesthetic for "Penginapan Kelapa Sawit"
     Features: Deep Emerald Forest, Palm Frond Silhouettes, Warm Canopy Sunburst,
     Smooth Parallax & Disappear-on-Scroll response.
     ═══════════════════════════════════════════════════════════════════════ --}}
<div class="absolute inset-0 overflow-hidden pointer-events-none select-none will-change-transform"
     :style="typeof scrollY !== 'undefined' ? `transform: translateY(${scrollY * 0.22}px); opacity: ${Math.max(0, 1 - scrollY / 420)}` : ''">

    {{-- Layer 1: Rich Multi-stop Deep Forest & Emerald Canopy Gradient Base --}}
    <div class="absolute inset-0"
         style="background: radial-gradient(ellipse 130% 90% at 50% -15%, #15803d 0%, #0d5427 30%, #052e16 65%, #021a0c 100%);"></div>
    
    {{-- Layer 2: Overhead Canopy Light Diffusion (Rich Forest Glow) --}}
    <div class="absolute inset-0 opacity-75"
         style="background: radial-gradient(ellipse 95% 75% at 50% 0%, rgba(34, 197, 94, 0.32) 0%, rgba(16, 185, 129, 0.18) 35%, rgba(5, 46, 22, 0.55) 75%, transparent 100%);"></div>

    {{-- Layer 3: Warm Ambient Golden Sunburst (Top Center Komorebi) --}}
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[650px] sm:w-[900px] h-[320px] sm:h-[420px] pointer-events-none opacity-45 blur-2xl"
         style="background: radial-gradient(circle, rgba(250, 204, 21, 0.22) 0%, rgba(74, 222, 128, 0.16) 45%, transparent 75%);
                animation: heroSunGlow 14s ease-in-out infinite;"></div>

    {{-- Layer 4: Organic Floating Glow Orbs for Soft Dynamic Depth --}}
    <div class="absolute -top-24 -left-20 w-[450px] h-[450px] rounded-full blur-3xl pointer-events-none opacity-40"
         style="background: radial-gradient(circle, rgba(52, 211, 153, 0.28) 0%, rgba(16, 185, 129, 0.12) 55%, transparent 75%);
                animation: heroOrb1 16s ease-in-out infinite;"></div>

    <div class="absolute top-1/4 -right-16 w-[480px] h-[480px] rounded-full blur-3xl pointer-events-none opacity-35"
         style="background: radial-gradient(circle, rgba(74, 222, 128, 0.28) 0%, rgba(5, 150, 105, 0.12) 55%, transparent 75%);
                animation: heroOrb2 20s ease-in-out infinite;"></div>

    {{-- Layer 5: Left Palm Frond Silhouette (Kelapa Sawit Tropical Theme) --}}
    <div class="absolute -top-12 -left-16 sm:-top-8 sm:-left-8 w-72 sm:w-96 md:w-[500px] h-72 sm:h-96 md:h-[500px] pointer-events-none text-emerald-400 opacity-20 sm:opacity-25"
         style="animation: heroPalmSwayLeft 24s ease-in-out infinite; transform-origin: top left;">
        <svg class="w-full h-full drop-shadow-[0_12px_24px_rgba(0,0,0,0.4)]" viewBox="0 0 600 600" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 0 C160 140 280 280 430 460 C480 520 520 580 540 600 C535 580 495 510 445 440 C310 260 180 130 0 0 Z" fill="currentColor" opacity="0.85"/>
            <path d="M480 500 C530 515 575 500 600 480 C565 478 520 485 475 492 Z" opacity="0.6"/>
            <path d="M470 490 C435 550 405 580 380 600 C405 570 435 535 465 480 Z" opacity="0.6"/>
            <path d="M440 445 C500 465 550 455 590 425 C550 430 495 435 435 433 Z" opacity="0.65"/>
            <path d="M430 435 C380 500 340 540 310 565 C340 530 385 480 425 425 Z" opacity="0.65"/>
            <path d="M395 380 C460 405 520 395 565 360 C520 370 460 375 390 369 Z" opacity="0.7"/>
            <path d="M385 370 C330 445 280 490 240 520 C280 475 330 420 380 360 Z" opacity="0.7"/>
            <path d="M345 310 C415 340 480 330 530 290 C480 305 415 310 340 300 Z" opacity="0.75"/>
            <path d="M335 300 C275 385 220 435 170 470 C215 420 270 360 330 290 Z" opacity="0.75"/>
            <path d="M290 240 C365 275 435 265 485 220 C435 240 365 245 285 230 Z" opacity="0.8"/>
            <path d="M280 230 C215 320 155 375 100 415 C150 360 210 295 275 220 Z" opacity="0.8"/>
            <path d="M230 165 C310 205 380 195 430 150 C380 170 310 175 225 155 Z" opacity="0.85"/>
            <path d="M220 155 C150 250 90 310 30 355 C85 295 150 225 215 145 Z" opacity="0.85"/>
            <path d="M165 90 C245 135 315 125 365 80 C315 100 245 105 160 80 Z" opacity="0.9"/>
            <path d="M155 80 C85 180 25 245 -30 290 C30 230 95 155 150 70 Z" opacity="0.9"/>
        </svg>
    </div>

    {{-- Layer 6: Right Palm Frond Silhouette (Kelapa Sawit Tropical Theme) --}}
    <div class="absolute -bottom-16 -right-16 sm:-bottom-10 sm:-right-8 w-72 sm:w-96 md:w-[480px] h-72 sm:h-96 md:h-[480px] pointer-events-none text-emerald-300 opacity-15 sm:opacity-20"
         style="animation: heroPalmSwayRight 28s ease-in-out infinite; transform-origin: bottom right;">
        <svg class="w-full h-full drop-shadow-[0_12px_24px_rgba(0,0,0,0.4)] rotate-180" viewBox="0 0 600 600" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 0 C160 140 280 280 430 460 C480 520 520 580 540 600 C535 580 495 510 445 440 C310 260 180 130 0 0 Z" fill="currentColor" opacity="0.85"/>
            <path d="M480 500 C530 515 575 500 600 480 C565 478 520 485 475 492 Z" opacity="0.6"/>
            <path d="M470 490 C435 550 405 580 380 600 C405 570 435 535 465 480 Z" opacity="0.6"/>
            <path d="M440 445 C500 465 550 455 590 425 C550 430 495 435 435 433 Z" opacity="0.65"/>
            <path d="M430 435 C380 500 340 540 310 565 C340 530 385 480 425 425 Z" opacity="0.65"/>
            <path d="M395 380 C460 405 520 395 565 360 C520 370 460 375 390 369 Z" opacity="0.7"/>
            <path d="M385 370 C330 445 280 490 240 520 C280 475 330 420 380 360 Z" opacity="0.7"/>
            <path d="M345 310 C415 340 480 330 530 290 C480 305 415 310 340 300 Z" opacity="0.75"/>
            <path d="M335 300 C275 385 220 435 170 470 C215 420 270 360 330 290 Z" opacity="0.75"/>
            <path d="M290 240 C365 275 435 265 485 220 C435 240 365 245 285 230 Z" opacity="0.8"/>
            <path d="M280 230 C215 320 155 375 100 415 C150 360 210 295 275 220 Z" opacity="0.8"/>
            <path d="M230 165 C310 205 380 195 430 150 C380 170 310 175 225 155 Z" opacity="0.85"/>
            <path d="M220 155 C150 250 90 310 30 355 C85 295 150 225 215 145 Z" opacity="0.85"/>
        </svg>
    </div>

    {{-- Layer 7: Elegant Diagonal Canopy Sunlight Rays (God rays) --}}
    <div class="absolute inset-0 opacity-20 pointer-events-none"
         style="background: linear-gradient(120deg, transparent 20%, rgba(255,255,255,0.05) 35%, rgba(134,239,172,0.12) 42%, rgba(255,255,255,0.03) 50%, transparent 68%);
                animation: heroRay 16s ease-in-out infinite;"></div>

    {{-- Layer 8: Delicate Organic Micro-Texture (Stippled Ambient, NOT harsh square grid) --}}
    <div class="absolute inset-0 opacity-20 pointer-events-none"
         style="background-image: radial-gradient(rgba(187, 247, 208, 0.4) 1px, transparent 1px);
                background-size: 24px 24px;"></div>

    {{-- Layer 9: Top Edge Crisp Shimmer Line --}}
    <div class="absolute top-0 left-0 right-0 h-px pointer-events-none"
         style="background: linear-gradient(90deg, transparent 0%, rgba(134,239,172,0.4) 25%, rgba(74,222,128,0.7) 50%, rgba(134,239,172,0.4) 75%, transparent 100%);"></div>

    {{-- Layer 10: Smooth Bottom Edge Vignette Fade into page content --}}
    <div class="absolute inset-0 bg-gradient-to-t from-primary-950/95 via-transparent to-black/30 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 right-0 h-28 bg-gradient-to-t from-primary-950 via-primary-950/80 to-transparent pointer-events-none"></div>
</div>

