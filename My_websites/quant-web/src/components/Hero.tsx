'use client';

import { useEffect, useRef } from 'react';
import Link from 'next/link';

export default function Hero() {
  const canvasRef = useRef<HTMLCanvasElement>(null);
  
  useEffect(() => {
    if (!canvasRef.current) return;
    
    const canvas = canvasRef.current;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;
    
    // Set canvas dimensions
    const resizeCanvas = () => {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
    };
    
    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();
    
    // Particle system
    const particlesArray: Particle[] = [];
    const particleCount = Math.min(100, Math.floor(window.innerWidth / 20));
    
    class Particle {
      x: number;
      y: number;
      size: number;
      speedX: number;
      speedY: number;
      color: string;
      
      constructor() {
        this.x = Math.random() * canvas.width;
        this.y = Math.random() * canvas.height;
        this.size = Math.random() * 3 + 1;
        this.speedX = (Math.random() - 0.5) * 0.5;
        this.speedY = (Math.random() - 0.5) * 0.5;
        this.color = `rgba(${Math.random() * 80 + 100}, ${Math.random() * 80 + 180}, ${Math.random() * 80 + 220}, ${Math.random() * 0.5 + 0.3})`;
      }
      
      update() {
        this.x += this.speedX;
        this.y += this.speedY;
        
        if (this.x > canvas.width) this.x = 0;
        else if (this.x < 0) this.x = canvas.width;
        
        if (this.y > canvas.height) this.y = 0;
        else if (this.y < 0) this.y = canvas.height;
      }
      
      draw() {
        if (!ctx) return;
        ctx.fillStyle = this.color;
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fill();
      }
    }
    
    const init = () => {
      for (let i = 0; i < particleCount; i++) {
        particlesArray.push(new Particle());
      }
    };
    
    const connectParticles = () => {
      if (!ctx) return;
      const maxDistance = 150;
      
      for (let a = 0; a < particlesArray.length; a++) {
        for (let b = a; b < particlesArray.length; b++) {
          const dx = particlesArray[a].x - particlesArray[b].x;
          const dy = particlesArray[a].y - particlesArray[b].y;
          const distance = Math.sqrt(dx * dx + dy * dy);
          
          if (distance < maxDistance) {
            const opacity = 1 - (distance / maxDistance);
            ctx.strokeStyle = `rgba(100, 220, 255, ${opacity * 0.4})`;
            ctx.lineWidth = 0.5;
            ctx.beginPath();
            ctx.moveTo(particlesArray[a].x, particlesArray[a].y);
            ctx.lineTo(particlesArray[b].x, particlesArray[b].y);
            ctx.stroke();
          }
        }
      }
    };
    
    const animate = () => {
      if (!ctx) return;
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      
      for (let i = 0; i < particlesArray.length; i++) {
        particlesArray[i].update();
        particlesArray[i].draw();
      }
      
      connectParticles();
      requestAnimationFrame(animate);
    };
    
    init();
    animate();
    
    return () => {
      window.removeEventListener('resize', resizeCanvas);
    };
  }, []);
  
  return (
    <section id="hero" className="relative min-h-screen flex items-center justify-center overflow-hidden">
      <canvas 
        ref={canvasRef} 
        className="absolute top-0 left-0 w-full h-full bg-transparent z-0"
      />
      
      <div className="grid-pattern absolute inset-0 opacity-30 z-0" />
      
      <div className="container relative z-10 mx-auto px-4 py-32 md:py-40 text-center">
        <h1 className="text-4xl md:text-6xl lg:text-7xl font-bold mb-6 animate-fadeIn gradient-text">
          Upgrade Your <br /> Digital Consumption
        </h1>
        
        <p className="text-lg md:text-xl max-w-3xl mx-auto mb-8 opacity-90 animate-slideIn">
          Replace mindless scrolling with scientific discovery. 
          Quant is a revolutionary platform offering scientific articles, images, 
          and skill-building content designed to make knowledge consumption as engaging as entertainment.
        </p>
        
        <div className="flex flex-col sm:flex-row justify-center gap-4 animate-slideIn" style={{ animationDelay: '0.2s' }}>
          <Link href="#features" className="sci-button">
            Explore Features
          </Link>
          
          <Link href="#science" className="sci-button">
            Discover Science
          </Link>
        </div>
      </div>
      
      <div className="absolute bottom-10 left-1/2 transform -translate-x-1/2 animate-float z-10">
        <div className="w-8 h-12 border-2 border-secondary rounded-full flex justify-center items-start p-1">
          <div className="w-1 h-3 bg-secondary rounded-full animate-pulse" />
        </div>
      </div>
    </section>
  );
} 