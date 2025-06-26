import Link from "next/link";
import {
    Car,
    Mail,
    Phone,
    MapPin,
    Facebook,
    Twitter,
    Instagram,
    Youtube,
} from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Separator } from "@/components/ui/separator";

export default function Footer() {
    const currentYear = new Date().getFullYear();
    const footerLinks = {
        perusahaan: [
            { href: "/tentang", label: "Tentang Kami" },
            { href: "/kontak", label: "Kontak" },
            { href: "/karir", label: "Karir" },
            { href: "/berita", label: "Berita" },
        ],
        layanan: [
            { href: "/mobil", label: "Katalog Mobil" },
            { href: "/janji-temu", label: "Janji Temu" },
            { href: "/pembiayaan", label: "Pembiayaan" },
            { href: "/tukar-tambah", label: "Tukar Tambah" },
        ],
        bantuan: [
            { href: "/faq", label: "FAQ" },
            { href: "/panduan", label: "Panduan Beli" },
            { href: "/garansi", label: "Garansi" },
            { href: "/bantuan", label: "Dukungan" },
        ],
    };

    const socialLinks = [
        { href: "#", icon: Facebook, label: "Facebook" },
        { href: "#", icon: Twitter, label: "Twitter" },
        { href: "#", icon: Instagram, label: "Instagram" },
        { href: "#", icon: Youtube, label: "YouTube" },
    ];

    return (
        <footer className="bg-muted/50 border-t ">
            <div className="container mx-auto px-4 lg:px-8">
                {/* Newsletter Section */}
                <div className="py-12 border-b">
                    <div className="grid gap-8 md:grid-cols-2 items-center">
                        <div className="space-y-2">
                            <h3 className="text-2xl font-bold">
                                Dapatkan Info Terbaru
                            </h3>
                            <p className="text-muted-foreground">
                                Berlangganan newsletter kami untuk mendapatkan
                                informasi mobil terbaru, promo menarik, dan tips
                                membeli mobil bekas.
                            </p>
                        </div>
                        <form className="flex gap-2">
                            <Input
                                type="email"
                                placeholder="Masukkan email Anda"
                                className="flex-1"
                            />
                            <Button type="submit">Berlangganan</Button>
                        </form>
                    </div>
                </div>

                {/* Main Footer Content */}
                <div className="py-12 grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                    {/* Company Info */}
                    <div className="space-y-4">
                        <div className="flex items-center space-x-2">
                            <Car className="h-6 w-6" />
                            <span className="text-xl font-bold">
                                MobilBekas
                            </span>
                        </div>
                        <p className="text-sm text-muted-foreground">
                            Platform terpercaya untuk jual beli mobil bekas
                            berkualitas. Kami menyediakan layanan terbaik dengan
                            harga yang kompetitif.
                        </p>
                        <div className="space-y-2">
                            <div className="flex items-center gap-2 text-sm">
                                <MapPin className="h-4 w-4" />
                                <span>Jl. Sudirman No. 123, Jakarta Pusat</span>
                            </div>
                            <div className="flex items-center gap-2 text-sm">
                                <Phone className="h-4 w-4" />
                                <span>+62 21 1234 5678</span>
                            </div>
                            <div className="flex items-center gap-2 text-sm">
                                <Mail className="h-4 w-4" />
                                <span>info@mobilbekas.com</span>
                            </div>
                        </div>
                    </div>

                    {/* Perusahaan Links */}
                    <div className="space-y-4">
                        <h4 className="font-semibold">Perusahaan</h4>
                        <ul className="space-y-2">
                            {footerLinks.perusahaan.map((link) => (
                                <li key={link.href}>
                                    <Link
                                        href={link.href}
                                        className="text-sm text-muted-foreground hover:text-primary transition-colors"
                                    >
                                        {link.label}
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    </div>

                    {/* Layanan Links */}
                    <div className="space-y-4">
                        <h4 className="font-semibold">Layanan</h4>
                        <ul className="space-y-2">
                            {footerLinks.layanan.map((link) => (
                                <li key={link.href}>
                                    <Link
                                        href={link.href}
                                        className="text-sm text-muted-foreground hover:text-primary transition-colors"
                                    >
                                        {link.label}
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    </div>

                    {/* Bantuan Links */}
                    <div className="space-y-4">
                        <h4 className="font-semibold">Bantuan</h4>
                        <ul className="space-y-2">
                            {footerLinks.bantuan.map((link) => (
                                <li key={link.href}>
                                    <Link
                                        href={link.href}
                                        className="text-sm text-muted-foreground hover:text-primary transition-colors"
                                    >
                                        {link.label}
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    </div>
                </div>

                <Separator />

                {/* Bottom Footer */}
                <div className="py-6 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div className="text-sm text-muted-foreground">
                        © {currentYear} MobilBekas. Semua hak dilindungi
                        undang-undang.
                    </div>

                    {/* Social Links */}
                    <div className="flex items-center gap-4">
                        {socialLinks.map((social) => {
                            const Icon = social.icon;
                            return (
                                <Link
                                    key={social.label}
                                    href={social.href}
                                    className="text-muted-foreground hover:text-primary transition-colors"
                                    aria-label={social.label}
                                >
                                    <Icon className="h-5 w-5" />
                                </Link>
                            );
                        })}
                    </div>

                    {/* Legal Links */}
                    <div className="flex items-center gap-4 text-sm">
                        <Link
                            href="/privacy"
                            className="text-muted-foreground hover:text-primary transition-colors"
                        >
                            Kebijakan Privasi
                        </Link>
                        <Link
                            href="/terms"
                            className="text-muted-foreground hover:text-primary transition-colors"
                        >
                            Syarat & Ketentuan
                        </Link>
                    </div>
                </div>
            </div>
        </footer>
    );
}
