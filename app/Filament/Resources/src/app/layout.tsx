import type { Metadata } from "next";
import { Inter } from "next/font/google";
import "./globals.css";
import Layout from "@/components/layout/layout";
import { Toaster } from "@/components/ui/sonner";

const inter = Inter({
    subsets: ["latin"],
    variable: "--font-inter",
});

export const metadata: Metadata = {
    metadataBase: new URL(
        process.env.NEXT_PUBLIC_SITE_URL || "http://localhost:3000"
    ),
    title: "Mobil Bekas Berkualitas | Sistem Penjualan Mobil Bekas",
    description:
        "Temukan mobil bekas berkualitas dengan harga terbaik. Berbagai merek dan model tersedia dengan garansi dan pelayanan terpercaya.",
};

export default function RootLayout({
    children,
}: Readonly<{
    children: React.ReactNode;
}>) {
    return (
        <html lang="id">
            <body className={`${inter.variable} font-sans antialiased`}>
                <Layout>{children}</Layout>
                <Toaster />
            </body>
        </html>
    );
}
