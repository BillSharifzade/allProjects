import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  reactCompiler: true,
  output: 'export',
  images: {
    unoptimized: true,
  },
  // Base path for GitHub Pages deployment: /repo-name
  basePath: '/allProjects',
};

export default nextConfig;
