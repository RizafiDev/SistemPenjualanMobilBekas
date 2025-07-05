import { Link } from "react-router-dom";

const navigationItems = [
    { name: "Beranda", href: "/" },
    { name: "Mobil", href: "/cars" },
    { name: "Artikel", href: "/articles" }, // ✅ Add articles navigation
    { name: "Tentang", href: "/about" },
    { name: "Kontak", href: "/contact" },
];

export function Navigation() {
    return (
        <nav>
            <ul>
                {navigationItems.map((item) => (
                    <li key={item.name}>
                        <Link to={item.href}>{item.name}</Link>
                    </li>
                ))}
            </ul>
        </nav>
    );
}