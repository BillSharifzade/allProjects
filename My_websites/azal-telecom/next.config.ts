import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  reactCompiler: true,
  output: 'export',
  images: {
    unoptimized: true,
  },
  // Base path for GitHub Pages deployment: /repo-name/path-to-folder
  basePath: '/allProjects/My_websites/azal-telecom',
};

export default nextConfig;
