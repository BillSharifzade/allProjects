'use client';

import React, { forwardRef, useRef } from 'react';
import { cn } from '@/lib/utils';
import { AnimatedBeam } from '@/components/magicui/AnimatedBeam';
import { Cloud, Database, Globe, MonitorSmartphone, Server, Shield, Wifi, Check } from 'lucide-react';

const Circle = forwardRef<HTMLDivElement, { className?: string; children?: React.ReactNode }>(
    ({ className, children }, ref) => {
        return (
            <div
                ref={ref}
                className={cn(
                    'z-10 flex size-10 md:size-14 items-center justify-center rounded-full glass p-2 md:p-3 shadow-[0_0_30px_-10px_rgba(168,85,247,0.5)]',
                    className
                )}
            >
                {children}
            </div>
        );
    }
);

Circle.displayName = 'Circle';

// Task #8: Added integration features list
const integrationFeatures = [
    'Real-time data synchronization',
    'Multi-cloud orchestration',
    'API gateway management',
    'Security compliance automation',
    'Network traffic optimization',
    'Automated failover systems'
];

export default function IntegrationsSection() {
    const containerRef = useRef<HTMLDivElement>(null);
    const centerRef = useRef<HTMLDivElement>(null);
    const ref1 = useRef<HTMLDivElement>(null);
    const ref2 = useRef<HTMLDivElement>(null);
    const ref3 = useRef<HTMLDivElement>(null);
    const ref4 = useRef<HTMLDivElement>(null);
    const ref5 = useRef<HTMLDivElement>(null);
    const ref6 = useRef<HTMLDivElement>(null);

    return (
        <section className="section relative overflow-hidden" id="integrations">
            <div className="max-w-7xl mx-auto px-4">
                <h2 className="section-title">Seamless Integrations</h2>
                <p className="section-subtitle">
                    Our unified platform connects all your infrastructure components, enabling seamless data flow and
                    real-time monitoring across your entire network.
                </p>

                <div className="grid lg:grid-cols-2 gap-8 md:gap-12 items-center mt-8 md:mt-12">
                    {/* Task #8: Added content to fill section */}
                    <div className="order-2 lg:order-1">
                        <h3 className="text-2xl md:text-3xl font-bold text-white mb-4 md:mb-6">
                            Connect Everything,<br />
                            <span className="gradient-text">Seamlessly</span>
                        </h3>
                        <p className="text-gray-400 text-sm md:text-base mb-6 leading-relaxed">
                            Our integration platform supports over 200+ enterprise applications and services.
                            From legacy systems to modern cloud infrastructure, we ensure your technology
                            stack works together harmoniously.
                        </p>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
                            {integrationFeatures.map((feature, index) => (
                                <div key={index} className="flex items-center gap-3">
                                    <div className="w-5 h-5 rounded-full bg-purple-500/20 flex items-center justify-center flex-shrink-0">
                                        <Check className="w-3 h-3 text-purple-400" />
                                    </div>
                                    <span className="text-gray-300 text-sm">{feature}</span>
                                </div>
                            ))}
                        </div>

                        <div className="mt-6 md:mt-8 flex gap-4">
                            <div className="glass rounded-lg px-4 py-3 text-center">
                                <div className="text-xl md:text-2xl font-bold gradient-text">200+</div>
                                <div className="text-xs text-gray-400">Integrations</div>
                            </div>
                            <div className="glass rounded-lg px-4 py-3 text-center">
                                <div className="text-xl md:text-2xl font-bold gradient-text">99.9%</div>
                                <div className="text-xs text-gray-400">API Uptime</div>
                            </div>
                            <div className="glass rounded-lg px-4 py-3 text-center">
                                <div className="text-xl md:text-2xl font-bold gradient-text">&lt;50ms</div>
                                <div className="text-xs text-gray-400">Response</div>
                            </div>
                        </div>
                    </div>

                    {/* Beam visualization */}
                    <div className="order-1 lg:order-2">
                        <div
                            className="relative flex h-[280px] md:h-[350px] w-full max-w-lg mx-auto items-center justify-center overflow-hidden rounded-2xl glass p-6 md:p-10"
                            ref={containerRef}
                        >
                            <div className="flex size-full max-h-[220px] md:max-h-[280px] flex-col items-stretch justify-between">
                                {/* Top Row */}
                                <div className="flex flex-row items-center justify-between">
                                    <Circle ref={ref1}>
                                        <Cloud className="w-4 h-4 md:w-6 md:h-6 text-purple-400" />
                                    </Circle>
                                    <Circle ref={ref4}>
                                        <Database className="w-4 h-4 md:w-6 md:h-6 text-purple-400" />
                                    </Circle>
                                </div>

                                {/* Middle Row */}
                                <div className="flex flex-row items-center justify-between">
                                    <Circle ref={ref2}>
                                        <Wifi className="w-4 h-4 md:w-6 md:h-6 text-purple-400" />
                                    </Circle>
                                    <Circle ref={centerRef} className="size-14 md:size-20 bg-gradient-to-br from-purple-500/20 to-indigo-500/20">
                                        <Globe className="w-7 h-7 md:w-10 md:h-10 text-purple-400" />
                                    </Circle>
                                    <Circle ref={ref5}>
                                        <Shield className="w-4 h-4 md:w-6 md:h-6 text-purple-400" />
                                    </Circle>
                                </div>

                                {/* Bottom Row */}
                                <div className="flex flex-row items-center justify-between">
                                    <Circle ref={ref3}>
                                        <Server className="w-4 h-4 md:w-6 md:h-6 text-purple-400" />
                                    </Circle>
                                    <Circle ref={ref6}>
                                        <MonitorSmartphone className="w-4 h-4 md:w-6 md:h-6 text-purple-400" />
                                    </Circle>
                                </div>
                            </div>

                            {/* Beams - Updated colors to lilac/purple theme */}
                            <AnimatedBeam
                                containerRef={containerRef}
                                fromRef={ref1}
                                toRef={centerRef}
                                curvature={-60}
                                gradientStartColor="#a855f7"
                                gradientStopColor="#6366f1"
                            />
                            <AnimatedBeam
                                containerRef={containerRef}
                                fromRef={ref2}
                                toRef={centerRef}
                                gradientStartColor="#a855f7"
                                gradientStopColor="#6366f1"
                            />
                            <AnimatedBeam
                                containerRef={containerRef}
                                fromRef={ref3}
                                toRef={centerRef}
                                curvature={60}
                                gradientStartColor="#a855f7"
                                gradientStopColor="#6366f1"
                            />
                            <AnimatedBeam
                                containerRef={containerRef}
                                fromRef={ref4}
                                toRef={centerRef}
                                curvature={-60}
                                reverse
                                gradientStartColor="#8b5cf6"
                                gradientStopColor="#a855f7"
                            />
                            <AnimatedBeam
                                containerRef={containerRef}
                                fromRef={ref5}
                                toRef={centerRef}
                                reverse
                                gradientStartColor="#8b5cf6"
                                gradientStopColor="#a855f7"
                            />
                            <AnimatedBeam
                                containerRef={containerRef}
                                fromRef={ref6}
                                toRef={centerRef}
                                curvature={60}
                                reverse
                                gradientStartColor="#8b5cf6"
                                gradientStopColor="#a855f7"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
