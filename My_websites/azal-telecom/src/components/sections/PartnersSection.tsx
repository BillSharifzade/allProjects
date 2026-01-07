'use client';

import LogoLoop from '@/components/components/LogoLoop';
import {
    SiAmazon,
    SiApple,
    SiCisco,
    SiDell,
    SiDropbox,
    SiGoogle,
    SiHuawei,
    SiIntel,
    SiNvidia,
    SiOracle,
    SiSalesforce,
    SiSap,
    SiSlack,
    SiVmware,
    SiZoom
} from 'react-icons/si';

// Task #7: Real company logos from react-icons/si
const partnerLogos = [
    { node: <SiGoogle className="w-10 h-10 text-white/60 hover:text-purple-400 transition-colors" />, title: 'Google' },
    { node: <SiAmazon className="w-10 h-10 text-white/60 hover:text-purple-400 transition-colors" />, title: 'Amazon' },
    { node: <SiApple className="w-10 h-10 text-white/60 hover:text-purple-400 transition-colors" />, title: 'Apple' },
    { node: <SiCisco className="w-10 h-10 text-white/60 hover:text-purple-400 transition-colors" />, title: 'Cisco' },
    { node: <SiIntel className="w-10 h-10 text-white/60 hover:text-purple-400 transition-colors" />, title: 'Intel' },
    { node: <SiOracle className="w-10 h-10 text-white/60 hover:text-purple-400 transition-colors" />, title: 'Oracle' },
    { node: <SiSalesforce className="w-10 h-10 text-white/60 hover:text-purple-400 transition-colors" />, title: 'Salesforce' },
    { node: <SiHuawei className="w-10 h-10 text-white/60 hover:text-purple-400 transition-colors" />, title: 'Huawei' },
    { node: <SiVmware className="w-10 h-10 text-white/60 hover:text-purple-400 transition-colors" />, title: 'VMware' },
    { node: <SiSap className="w-10 h-10 text-white/60 hover:text-purple-400 transition-colors" />, title: 'SAP' },
    { node: <SiNvidia className="w-10 h-10 text-white/60 hover:text-purple-400 transition-colors" />, title: 'Nvidia' },
    { node: <SiDell className="w-10 h-10 text-white/60 hover:text-purple-400 transition-colors" />, title: 'Dell' },
    { node: <SiSlack className="w-10 h-10 text-white/60 hover:text-purple-400 transition-colors" />, title: 'Slack' },
    { node: <SiZoom className="w-10 h-10 text-white/60 hover:text-purple-400 transition-colors" />, title: 'Zoom' },
    { node: <SiDropbox className="w-10 h-10 text-white/60 hover:text-purple-400 transition-colors" />, title: 'Dropbox' }
];

export default function PartnersSection() {
    return (
        <section className="py-12 md:py-16 relative overflow-hidden border-y border-white/5">
            <div className="max-w-7xl mx-auto px-4">
                <p className="text-center text-gray-500 text-xs md:text-sm uppercase tracking-wider mb-6 md:mb-8">
                    Trusted by Industry Leaders
                </p>

                <div className="h-[60px] md:h-[80px] relative">
                    <LogoLoop
                        logos={partnerLogos}
                        speed={50}
                        direction="left"
                        logoHeight={40}
                        gap={60}
                        fadeOut
                        fadeOutColor="#050510"
                        scaleOnHover
                        hoverSpeed={25}
                    />
                </div>
            </div>
        </section>
    );
}
