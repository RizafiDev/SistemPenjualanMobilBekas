"use client";

import Link from "next/link";
import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Sheet, SheetContent, SheetTrigger } from "@/components/ui/sheet";
import { Menu, Car, Phone, Search } from "lucide-react";

export default function Header() {
    const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

    // Remove hooks temporarily to isolate the issue
    // const { data: mereksData } = useMereks();
    // const { data: kategorisData } = useKategoris();

    // Mock data for now
    const mereks: any[] = [];
    const kategoris: any[] = [];

    const mainNavItems = [
        { href: "/", label: "Beranda" },
        { href: "/mobil", label: "Katalog Mobil" },
        { href: "/janji-temu", label: "Buat Janji Temu" },
        { href: "/kontak", label: "Kontak" },
    ];

    return (
        <header className="fixed top-0 z-50 w-full  border-b border-gray-200/50 bg-white/80 backdrop-blur-xl supports-[backdrop-filter]:bg-white/60 shadow-sm">
            <div className="container flex h-16 items-center justify-between mx-auto px-4 lg:px-8">
                {/* Logo */}
                <Link href="/" className="flex items-center space-x-3 group">
                    <div className="relative">
                        <div className="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg blur opacity-75 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div className="relative bg-gradient-to-r from-blue-600 to-purple-600 p-2 rounded-lg">
                            <Car className="h-6 w-6 text-white" />
                        </div>
                    </div>
                    <span className="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        MobilBekas
                    </span>
                </Link>

                {/* Desktop Navigation */}
                <nav className="hidden md:flex items-center space-x-8">
                    <Link
                        href="/"
                        className="relative text-sm font-medium text-gray-700 transition-all duration-300 hover:text-blue-600 after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-gradient-to-r after:from-blue-600 after:to-purple-600 after:transition-all after:duration-300 hover:after:w-full"
                    >
                        Beranda
                    </Link>
                    <Link
                        href="/mobil"
                        className="relative text-sm font-medium text-gray-700 transition-all duration-300 hover:text-blue-600 after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-gradient-to-r after:from-blue-600 after:to-purple-600 after:transition-all after:duration-300 hover:after:w-full"
                    >
                        Katalog
                    </Link>
                    <Link
                        href="/janji-temu"
                        className="relative text-sm font-medium text-gray-700 transition-all duration-300 hover:text-blue-600 after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-gradient-to-r after:from-blue-600 after:to-purple-600 after:transition-all after:duration-300 hover:after:w-full"
                    >
                        Janji Temu
                    </Link>
                    <Link
                        href="/kontak"
                        className="relative text-sm font-medium text-gray-700 transition-all duration-300 hover:text-blue-600 after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-gradient-to-r after:from-blue-600 after:to-purple-600 after:transition-all after:duration-300 hover:after:w-full"
                    >
                        Kontak
                    </Link>
                </nav>

                {/* Desktop CTA Buttons */}
                <div className="hidden md:flex items-center space-x-3">
                    <Button 
                        variant="outline" 
                        size="sm" 
                        asChild
                        className="border-2 border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition-all duration-300"
                    >
                        <Link href="/mobil">
                            <Search className="mr-2 h-4 w-4" />
                            Cari Mobil
                        </Link>
                    </Button>
                    <Button 
                        size="sm" 
                        asChild
                        className="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white shadow-lg hover:shadow-xl transition-all duration-300"
                    >
                        <Link href="/kontak">
                            <Phone className="mr-2 h-4 w-4" />
                            Hubungi Kami
                        </Link>
                    </Button>
                </div>

                {/* Mobile Menu */}
                <Sheet
                    open={isMobileMenuOpen}
                    onOpenChange={setIsMobileMenuOpen}
                >
                    <SheetTrigger asChild className="md:hidden">
                        <Button 
                            variant="ghost" 
                            size="icon"
                            className="hover:bg-gray-100 transition-colors duration-300"
                        >
                            <Menu className="h-5 w-5" />
                            <span className="sr-only">
                                Toggle navigation menu
                            </span>
                        </Button>
                    </SheetTrigger>
                    <SheetContent
                        side="right"
                        className="w-[300px] sm:w-[400px] bg-white/95 backdrop-blur-xl"
                    >
                        <nav className="flex flex-col space-y-4 mt-8">
                            {mainNavItems.map((item) => (
                                <Link
                                    key={item.href}
                                    href={item.href}
                                    onClick={() => setIsMobileMenuOpen(false)}
                                    className="flex items-center text-lg font-medium transition-all duration-300 hover:text-blue-600 hover:translate-x-2 px-4 py-2 rounded-lg hover:bg-blue-50"
                                >
                                    {item.label}
                                </Link>
                            ))}

                            {mereks.length > 0 && (
                                <div className="border-t pt-4 mt-4">
                                    <h4 className="font-medium mb-2 px-4 text-gray-900">
                                        Merek Populer
                                    </h4>
                                    {mereks.slice(0, 3).map((merek) => (
                                        <Link
                                            key={merek.id}
                                            href={`/brand/${merek.id}`}
                                            onClick={() =>
                                                setIsMobileMenuOpen(false)
                                            }
                                            className="block py-2 px-4 text-sm text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-300"
                                        >
                                            {merek.nama_merek}
                                        </Link>
                                    ))}
                                </div>
                            )}

                            {kategoris.length > 0 && (
                                <div className="border-t pt-4">
                                    <h4 className="font-medium mb-2 px-4 text-gray-900">
                                        Kategori
                                    </h4>
                                    {kategoris.slice(0, 3).map((kategori) => (
                                        <Link
                                            key={kategori.id}
                                            href={`/category/${kategori.id}`}
                                            onClick={() =>
                                                setIsMobileMenuOpen(false)
                                            }
                                            className="block py-2 px-4 text-sm text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-300"
                                        >
                                            {kategori.nama_kategori}
                                        </Link>
                                    ))}
                                </div>
                            )}

                            <div className="flex flex-col space-y-3 pt-6 px-4">
                                <Button 
                                    asChild
                                    className="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white shadow-lg hover:shadow-xl transition-all duration-300"
                                >
                                    <Link
                                        href="/mobil"
                                        onClick={() =>
                                            setIsMobileMenuOpen(false)
                                        }
                                    >
                                        <Search className="mr-2 h-4 w-4" />
                                        Cari Mobil
                                    </Link>
                                </Button>
                                <Button 
                                    variant="outline" 
                                    asChild
                                    className="border-2 border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition-all duration-300"
                                >
                                    <Link
                                        href="/kontak"
                                        onClick={() =>
                                            setIsMobileMenuOpen(false)
                                        }
                                    >
                                        <Phone className="mr-2 h-4 w-4" />
                                        Hubungi Kami
                                    </Link>
                                </Button>
                            </div>
                        </nav>
                    </SheetContent>
                </Sheet>
            </div>
        </header>
    );
}
