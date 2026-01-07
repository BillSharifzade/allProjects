'use client';

import dynamic from 'next/dynamic';
import { ScrollProgressProvider, ScrollProgress } from '@/components/animate-ui/primitives/animate/scroll-progress';
import HeroSection from '@/components/sections/HeroSection';
import PartnersSection from '@/components/sections/PartnersSection';
import ServicesSection from '@/components/sections/ServicesSection';
import CTASection from '@/components/sections/CTASection';

// Lazy load heavy WebGL components - only render when needed
const GlobalSection = dynamic(() => import('@/components/sections/GlobalSection'), {
  loading: () => (
    <div className="section h-[600px] flex items-center justify-center">
      <div className="text-gray-500">Loading globe...</div>
    </div>
  ),
  ssr: false
});

const IntegrationsSection = dynamic(() => import('@/components/sections/IntegrationsSection'), {
  loading: () => <div className="section h-[400px]" />,
  ssr: false
});

const FeaturesSection = dynamic(() => import('@/components/sections/FeaturesSection'), {
  loading: () => <div className="section h-[400px]" />,
  ssr: false
});

export default function Home() {
  return (
    <ScrollProgressProvider global direction="vertical">
      {/* Fixed Scroll Progress Bar */}
      <div className="fixed top-0 left-0 right-0 z-[100]">
        <ScrollProgress className="h-1 bg-gradient-to-r from-purple-500 via-indigo-500 to-purple-500" />
      </div>

      {/* Main Content */}
      <main className="relative">
        {/* Hero Section - Threads + TextType + StarBorder */}
        <HeroSection />

        {/* Partners Section - LogoLoop */}
        <PartnersSection />

        {/* Services Section - PixelCards */}
        <ServicesSection />

        {/* Global Presence Section - Globe (Lazy loaded) */}
        <GlobalSection />

        {/* Integrations Section - AnimatedBeam (Lazy loaded) */}
        <IntegrationsSection />

        {/* Features Section - Carousel (Lazy loaded) */}
        <FeaturesSection />

        {/* CTA Section - StarBorder */}
        <CTASection />
      </main>
    </ScrollProgressProvider>
  );
}
