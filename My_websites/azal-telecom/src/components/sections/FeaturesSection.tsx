'use client';

import Carousel, { CarouselItem } from '@/components/components/Carousel';
import { Award, Headphones, Rocket, Zap } from 'lucide-react';

// Updated colors to lilac/purple theme
const features: CarouselItem[] = [
    {
        title: '24/7 Support',
        description: 'Round-the-clock expert support with average response time under 5 minutes.',
        id: 1,
        icon: <Headphones className="w-4 h-4 text-purple-400" />
    },
    {
        title: 'Blazing Fast',
        description: 'Ultra-low latency network with speeds up to 100Gbps for enterprise clients.',
        id: 2,
        icon: <Zap className="w-4 h-4 text-purple-400" />
    },
    {
        title: 'Award Winning',
        description: 'Recognized as Best Telecom Provider 2026 by Global Tech Awards.',
        id: 3,
        icon: <Award className="w-4 h-4 text-purple-400" />
    },
    {
        title: 'Future Ready',
        description: 'Cutting-edge infrastructure ready for 6G, quantum networking, and beyond.',
        id: 4,
        icon: <Rocket className="w-4 h-4 text-purple-400" />
    }
];

export default function FeaturesSection() {
    return (
        <section className="section relative overflow-hidden" id="features">
            <div className="max-w-7xl mx-auto px-4">
                <h2 className="section-title">Why Choose Us</h2>
                <p className="section-subtitle">
                    Experience the difference with Azal Telecom&apos;s industry-leading features and commitment to
                    excellence.
                </p>

                {/* Task #9: Mobile responsive carousel */}
                <div className="flex justify-center mt-8 md:mt-12">
                    <Carousel
                        items={features}
                        baseWidth={300}
                        autoplay={true}
                        autoplayDelay={4000}
                        pauseOnHover={true}
                        loop={true}
                    />
                </div>
            </div>
        </section>
    );
}
