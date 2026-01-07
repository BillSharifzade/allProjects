'use client';

import PixelCard from '@/components/components/PixelCard';
import { Cloud, Shield, Wifi } from 'lucide-react';

const services = [
    {
        icon: Cloud,
        title: 'Cloud Infrastructure',
        description:
            'Enterprise-grade cloud solutions with global data centers. Scale your operations seamlessly with our hybrid cloud architecture.',
        variant: 'blue' as const
    },
    {
        icon: Wifi,
        title: 'Network Solutions',
        description:
            'High-speed fiber optic networks and 5G connectivity. Experience ultra-low latency and maximum throughput for your business.',
        variant: 'default' as const
    },
    {
        icon: Shield,
        title: 'Cybersecurity',
        description:
            'Advanced threat protection and compliance solutions. Safeguard your digital assets with our 24/7 security operations center.',
        variant: 'pink' as const
    }
];

export default function ServicesSection() {
    return (
        <section className="section relative overflow-hidden" id="services">
            <div className="max-w-7xl mx-auto px-4">
                <h2 className="section-title">Our Services</h2>
                <p className="section-subtitle">
                    Comprehensive telecommunications solutions designed for the modern enterprise. From infrastructure
                    to security, we&apos;ve got you covered.
                </p>

                {/* Task #9: Mobile responsive grid */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8 mt-8 md:mt-12">
                    {services.map((service, index) => (
                        <div key={index} className="flex justify-center">
                            <PixelCard variant={service.variant} className="w-full max-w-[350px] h-[350px] md:h-[400px]">
                                <div className="absolute inset-0 flex flex-col items-center justify-center p-6 md:p-8 text-center">
                                    <div className="w-12 h-12 md:w-16 md:h-16 rounded-2xl bg-gradient-to-br from-purple-500/20 to-indigo-500/20 flex items-center justify-center mb-4 md:mb-6">
                                        <service.icon className="w-6 h-6 md:w-8 md:h-8 text-purple-400" />
                                    </div>
                                    <h3 className="text-xl md:text-2xl font-bold text-white mb-3 md:mb-4">{service.title}</h3>
                                    <p className="text-gray-400 text-sm md:text-base leading-relaxed">{service.description}</p>
                                </div>
                            </PixelCard>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
