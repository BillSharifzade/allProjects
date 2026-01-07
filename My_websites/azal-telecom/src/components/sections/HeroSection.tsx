'use client';

import Threads from '@/components/backgrounds/Threads';
import TextType from '@/components/text-animations/TextType';
import StarBorder from '@/components/components/StarBorder';

export default function HeroSection() {
    return (
        <section className="relative min-h-screen flex items-center justify-center overflow-hidden">
            {/* Threads Background - with mouse interaction (Task #5) */}
            <div className="absolute inset-0 z-0">
                <Threads color={[0.6, 0.4, 0.9]} amplitude={0.8} distance={0.15} enableMouseInteraction={true} />
            </div>

            {/* Gradient Overlay - lilac/deep blue theme (Task #3) */}
            <div className="absolute inset-0 bg-gradient-to-b from-transparent via-[#0a0a20]/30 to-[#050510] z-[1] pointer-events-none" />

            {/* Content */}
            <div className="relative z-10 text-center px-4 max-w-5xl mx-auto">
                <h1 className="text-5xl md:text-7xl lg:text-8xl font-bold mb-6">
                    <span className="text-white">Connect the</span>
                    <br />
                    <span className="gradient-text">Future</span>
                </h1>

                <div className="text-xl md:text-2xl text-gray-300 mb-8 h-16 flex items-center justify-center">
                    <TextType
                        text={[
                            'Enterprise Cloud Solutions',
                            'Global Network Infrastructure',
                            '5G & IoT Connectivity',
                            'Cybersecurity Excellence',
                            'Digital Transformation'
                        ]}
                        typingSpeed={50}
                        deletingSpeed={30}
                        pauseDuration={2000}
                        showCursor={true}
                        cursorCharacter="_"
                        className="text-purple-300"
                    />
                </div>

                <p className="text-gray-400 text-lg max-w-2xl mx-auto mb-12">
                    Azal Telecom delivers cutting-edge telecommunications infrastructure and solutions for enterprises
                    worldwide. Powering the digital backbone of tomorrow.
                </p>

                <div className="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <StarBorder as="button" color="#a855f7" speed="4s" className="text-lg">
                        Get Started
                    </StarBorder>

                    <StarBorder as="button" color="#6366f1" speed="5s" className="text-lg">
                        Learn More
                    </StarBorder>
                </div>

                {/* Stats */}
                <div className="grid grid-cols-3 gap-4 md:gap-8 mt-16 md:mt-20 max-w-3xl mx-auto">
                    <div className="text-center">
                        <div className="text-3xl md:text-5xl font-bold gradient-text">150+</div>
                        <div className="text-gray-400 text-sm md:text-base mt-2">Countries Served</div>
                    </div>
                    <div className="text-center">
                        <div className="text-3xl md:text-5xl font-bold gradient-text">99.9%</div>
                        <div className="text-gray-400 text-sm md:text-base mt-2">Uptime SLA</div>
                    </div>
                    <div className="text-center">
                        <div className="text-3xl md:text-5xl font-bold gradient-text">10M+</div>
                        <div className="text-gray-400 text-sm md:text-base mt-2">Active Users</div>
                    </div>
                </div>
            </div>

            {/* Scroll Indicator */}
            <div className="absolute bottom-8 left-1/2 -translate-x-1/2 z-10 animate-bounce hidden md:block">
                <div className="w-6 h-10 rounded-full border-2 border-purple-400/50 flex items-start justify-center p-2">
                    <div className="w-1 h-2 bg-purple-400 rounded-full" />
                </div>
            </div>
        </section>
    );
}
