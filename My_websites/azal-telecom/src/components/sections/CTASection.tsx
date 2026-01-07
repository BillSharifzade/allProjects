'use client';

import StarBorder from '@/components/components/StarBorder';
import { ArrowRight, Mail, Phone } from 'lucide-react';

export default function CTASection() {
    return (
        <section className="section relative overflow-hidden" id="contact">
            <div className="max-w-4xl mx-auto px-4 text-center">
                <h2 className="text-3xl md:text-5xl lg:text-6xl font-bold mb-6">
                    Ready to <span className="gradient-text">Transform</span> Your Network?
                </h2>
                <p className="text-gray-400 text-base md:text-lg max-w-2xl mx-auto mb-8 md:mb-12">
                    Join thousands of enterprises already powered by Azal Telecom. Get started with a free consultation
                    and discover what&apos;s possible.
                </p>

                <div className="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12 md:mb-16">
                    <StarBorder as="button" color="#a855f7" speed="3s" className="text-base md:text-lg group">
                        <span className="flex items-center gap-2">
                            Start Free Trial
                            <ArrowRight className="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                        </span>
                    </StarBorder>

                    <StarBorder as="button" color="#6366f1" speed="4s" className="text-base md:text-lg">
                        Schedule Demo
                    </StarBorder>
                </div>

                <div className="glass rounded-2xl p-6 md:p-8 max-w-2xl mx-auto">
                    <div className="grid sm:grid-cols-2 gap-4 md:gap-6">
                        <div className="flex items-center justify-center gap-3 text-gray-300">
                            <div className="w-10 h-10 md:w-12 md:h-12 rounded-full bg-purple-500/10 flex items-center justify-center">
                                <Mail className="w-4 h-4 md:w-5 md:h-5 text-purple-400" />
                            </div>
                            <div className="text-left">
                                <div className="text-xs md:text-sm text-gray-500">Email us</div>
                                <div className="text-white text-sm md:text-base">contact@azaltelecom.com</div>
                            </div>
                        </div>
                        <div className="flex items-center justify-center gap-3 text-gray-300">
                            <div className="w-10 h-10 md:w-12 md:h-12 rounded-full bg-purple-500/10 flex items-center justify-center">
                                <Phone className="w-4 h-4 md:w-5 md:h-5 text-purple-400" />
                            </div>
                            <div className="text-left">
                                <div className="text-xs md:text-sm text-gray-500">Call us</div>
                                <div className="text-white text-sm md:text-base">+1 (555) 123-4567</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Footer - Task #4: Updated year to 2026 */}
            <footer className="mt-16 md:mt-24 pt-8 border-t border-white/5">
                <div className="max-w-7xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div className="text-xl md:text-2xl font-bold gradient-text">Azal Telecom</div>
                    <div className="text-gray-500 text-xs md:text-sm text-center md:text-left">
                        © 2026 Azal Telecom. All rights reserved. Connecting the future.
                    </div>
                </div>
            </footer>
        </section>
    );
}
