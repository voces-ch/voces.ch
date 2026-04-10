import { defineConfig, loadEnv } from "vite";
import vue from "@vitejs/plugin-vue";
import cssInjectedByJsPlugin from "vite-plugin-css-injected-by-js";
import tailwindcss from "@tailwindcss/vite";
import path from "path";
import fs from "fs";

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, "../", "");
    const versionEnv = loadEnv("versioning", process.cwd(), "");
    const version = versionEnv.WIDGET_VERSION || "0.0.1";
    const widgetRoot = path.resolve(__dirname, "../public/widget");
    const versionedPath = path.join(widgetRoot, version);
    const latestPath = path.join(widgetRoot, "latest");

    return {
        plugins: [
            vue({
                template: {
                    compilerOptions: {
                        isCustomElement: (tag) => tag.startsWith("altcha-"),
                    },
                },
            }),
            cssInjectedByJsPlugin(),
            tailwindcss(),
            {
                name: "mirror-to-latest",
                closeBundle() {
                    if (mode === "production") {
                        console.log(`\n📦 Build finished in: ${versionedPath}`);
                        if (!fs.existsSync(widgetRoot)) {
                            fs.mkdirSync(widgetRoot, { recursive: true });
                        }
                        if (fs.existsSync(versionedPath)) {
                            console.log(`✨ Mirroring to ${latestPath}...`);

                            // Ensure 'latest' directory exists
                            fs.mkdirSync(latestPath, { recursive: true });

                            // Copy the contents
                            fs.cpSync(versionedPath, latestPath, {
                                recursive: true,
                                force: true,
                            });

                            console.log(`✅ Success! Deployment ready.\n`);
                        } else {
                            console.error(
                                `❌ Error: Source directory ${versionedPath} was not found after build!`,
                            );
                        }
                    }
                },
            },
        ],
        define: {
            "import.meta.env.VITE_API_URL": JSON.stringify(
                `${env.APP_URL}/api/v1`,
            ),
            "import.meta.env.VITE_WIDGET_VERSION": JSON.stringify(version),
        },
        server: {
            host: "0.0.0.0",
            port: 5173,
            hmr: {
                host: "widget.voces.lndo.site",
                clientPort: 443,
            },
        },
        esbuild: {
            drop: mode === "production" ? ["console", "debugger"] : [],
        },
        build: {
            outDir: versionedPath,
            emptyOutDir: true,

            rollupOptions: {
                input: "src/main.js",
                output: {
                    entryFileNames: `voces-widget.js`,
                    chunkFileNames: `voces-widget.js`,
                    assetFileNames: `[name].[ext]`,
                },
            },
        },
    };
});
