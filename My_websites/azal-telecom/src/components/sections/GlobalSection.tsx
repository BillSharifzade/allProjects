'use client';

import { Globe } from '@/components/magicui/Globe';
import Threads from '@/components/backgrounds/Threads';
import { COBEOptions } from 'cobe';

// Task #6: Updated globe colors to match website theme (lilac/deep blue)
const GLOBE_CONFIG: COBEOptions = {
    width: 800,
    height: 800,
    onRender: () => { },
    devicePixelRatio: 1.5,
    phi: 0,
    theta: 0.3,
    dark: 1,
    diffuse: 1.2,
    mapSamples: 6000,
    mapBrightness: 2.5,
    baseColor: [0.1, 0.1, 0.2], // Slightly brighter base
    markerColor: [0.66, 0.33, 0.97], // Lilac (#a855f7)
    glowColor: [0.6, 0.5, 1], // Brighter purple-blue glow
    markers: [
        // Major cities where Azal Telecom operates
        { location: [40.7128, -74.006], size: 0.1 }, // New York
        { location: [51.5074, -0.1278], size: 0.08 }, // London
        { location: [35.6762, 139.6503], size: 0.08 }, // Tokyo
        { location: [1.3521, 103.8198], size: 0.07 }, // Singapore
        { location: [25.2048, 55.2708], size: 0.09 }, // Dubai
        { location: [-33.8688, 151.2093], size: 0.06 }, // Sydney
        { location: [52.52, 13.405], size: 0.06 }, // Berlin
        { location: [37.7749, -122.4194], size: 0.08 }, // San Francisco
        { location: [19.076, 72.8777], size: 0.07 }, // Mumbai
        { location: [-23.5505, -46.6333], size: 0.06 }, // São Paulo
        { location: [31.2304, 121.4737], size: 0.08 }, // Shanghai
        { location: [55.7558, 37.6173], size: 0.06 } // Moscow
    ]
};

export default function GlobalSection() {
    return (
        <section className="section relative overflow-hidden" id="global">
            {/* Background Threads */}
            <div className="absolute inset-0 z-0 opacity-40 pointer-events-none">
                <Threads color={[0.4, 0.2, 0.8]} amplitude={1.2} distance={0.2} enableMouseInteraction={true} />
            </div>

            <div className="max-w-7xl mx-auto px-4 relative z-10">
                <div className="grid lg:grid-cols-2 gap-8 md:gap-12 items-center">
                    {/* Content */}
                    <div className="order-2 lg:order-1">
                        <h2 className="text-3xl md:text-5xl lg:text-6xl font-bold mb-4 md:mb-6">
                            <span className="text-white">Global</span>
                            <br />
                            <span className="gradient-text">Presence</span>
                        </h2>
                        <p className="text-gray-400 text-base md:text-lg mb-6 md:mb-8 leading-relaxed">
                            With data centers and network points of presence across 6 continents, Azal Telecom delivers
                            ultra-low latency connectivity to enterprises worldwide.
                        </p>

                        <div className="grid grid-cols-2 gap-3 md:gap-6">
                            <div className="glass rounded-xl p-4 md:p-6">
                                <div className="text-2xl md:text-3xl font-bold gradient-text mb-1 md:mb-2">50+</div>
                                <div className="text-gray-400 text-sm md:text-base">Data Centers</div>
                            </div>
                            <div className="glass rounded-xl p-4 md:p-6">
                                <div className="text-2xl md:text-3xl font-bold gradient-text mb-1 md:mb-2">200+</div>
                                <div className="text-gray-400 text-sm md:text-base">PoPs Worldwide</div>
                            </div>
                            <div className="glass rounded-xl p-4 md:p-6">
                                <div className="text-2xl md:text-3xl font-bold gradient-text mb-1 md:mb-2">&lt;10ms</div>
                                <div className="text-gray-400 text-sm md:text-base">Average Latency</div>
                            </div>
                            <div className="glass rounded-xl p-4 md:p-6">
                                <div className="text-2xl md:text-3xl font-bold gradient-text mb-1 md:mb-2">100Tbps</div>
                                <div className="text-gray-400 text-sm md:text-base">Network Capacity</div>
                            </div>
                        </div>
                    </div>

                    {/* Globe - Centered with edge blending */}
                    <div className="order-1 lg:order-2 flex items-center justify-center">
                        <div className="relative w-[350px] h-[350px] md:w-[450px] md:h-[450px] lg:w-[500px] lg:h-[500px] rounded-[3rem] overflow-hidden flex items-center justify-center border border-white/5 bg-gradient-to-br from-white/10 to-transparent backdrop-blur-[2px]">
                            <div className="absolute inset-0 flex items-center justify-center scale-[1.15]">
                                <Globe config={{ ...GLOBE_CONFIG, mapBrightness: 3.0 }} className="!mx-0" />
                            </div>
                            {/* Radial gradient mask to blend globe edges - widened for better visibility */}
                            <div
                                className="absolute inset-0 pointer-events-none"
                                style={{
                                    background: 'radial-gradient(circle at center, transparent 45%, #050510 85%)'
                                }}
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
