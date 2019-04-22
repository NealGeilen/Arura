-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Gegenereerd op: 22 apr 2019 om 20:16
-- Serverversie: 5.7.23
-- PHP-versie: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webserver`
--
CREATE DATABASE IF NOT EXISTS `webserver` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `webserver`;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblcmsaddons`
--

DROP TABLE IF EXISTS `tblcmsaddons`;
CREATE TABLE IF NOT EXISTS `tblcmsaddons` (
  `Addon_Id` int(11) NOT NULL AUTO_INCREMENT,
  `Addon_Name` varchar(255) COLLATE utf8_bin NOT NULL,
  `Addon_FileName` varchar(255) COLLATE utf8_bin NOT NULL,
  `Addon_Type` varchar(255) COLLATE utf8_bin NOT NULL,
  `Addon_Active` tinyint(1) NOT NULL,
  `Addon_Multipel_Values` tinyint(1) NOT NULL,
  PRIMARY KEY (`Addon_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Gegevens worden geëxporteerd voor tabel `tblcmsaddons`
--

INSERT INTO `tblcmsaddons` (`Addon_Id`, `Addon_Name`, `Addon_FileName`, `Addon_Type`, `Addon_Active`, `Addon_Multipel_Values`) VALUES
(5, 'Cards', 'plg.cards.php', 'widget', 1, 1),
(6, 'Milestones', 'awdawdawdawd', 'widget', 1, 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblcmsaddonsettings`
--

DROP TABLE IF EXISTS `tblcmsaddonsettings`;
CREATE TABLE IF NOT EXISTS `tblcmsaddonsettings` (
  `AddonSetting_Id` int(11) NOT NULL AUTO_INCREMENT,
  `AddonSetting_Addon_Id` int(11) NOT NULL,
  `AddonSetting_Type` varchar(255) COLLATE utf8_bin NOT NULL,
  `AddonSetting_Position` int(11) NOT NULL,
  `AddonSetting_Tag` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`AddonSetting_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Gegevens worden geëxporteerd voor tabel `tblcmsaddonsettings`
--

INSERT INTO `tblcmsaddonsettings` (`AddonSetting_Id`, `AddonSetting_Addon_Id`, `AddonSetting_Type`, `AddonSetting_Position`, `AddonSetting_Tag`) VALUES
(1, 5, 'TextArea', 1, 'text'),
(2, 5, 'Picture', 0, 'img'),
(3, 6, 'Icon', 0, 'Icon'),
(4, 6, 'TextArea', 1, 'Text');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblcmscontentblocks`
--

DROP TABLE IF EXISTS `tblcmscontentblocks`;
CREATE TABLE IF NOT EXISTS `tblcmscontentblocks` (
  `Content_Id` int(11) NOT NULL AUTO_INCREMENT,
  `Content_Group_Id` int(11) DEFAULT NULL,
  `Content_Addon_Id` int(11) DEFAULT NULL,
  `Content_Type` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `Content_Value` longtext COLLATE utf8_bin,
  `Content_Position` int(11) DEFAULT NULL,
  `Content_Size` int(11) DEFAULT NULL,
  `Content_Raster` int(11) DEFAULT '2',
  `Content_Css_Background_Color` varchar(11) COLLATE utf8_bin DEFAULT NULL,
  `Content_Css_Background_Img` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`Content_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=93 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Gegevens worden geëxporteerd voor tabel `tblcmscontentblocks`
--

INSERT INTO `tblcmscontentblocks` (`Content_Id`, `Content_Group_Id`, `Content_Addon_Id`, `Content_Type`, `Content_Value`, `Content_Position`, `Content_Size`, `Content_Raster`, `Content_Css_Background_Color`, `Content_Css_Background_Img`) VALUES
(76, 47, 0, 'Picture', 'vmzinc.png', 2, 4, 2, '', ''),
(77, 47, 0, 'Picture', 'velux.png', 3, 4, 2, '', ''),
(78, 47, 0, 'TextArea', '<h1 class=\"text-center text-primary\" style=\"margin-top: 0px; margin-bottom: 0.5rem; font-family: Montserrat; font-weight: bold; line-height: 1.2; font-size: 2.5rem; letter-spacing: normal; color: navy !important;\">Onze partners</h1>', 0, 12, 2, '', ''),
(73, 47, 0, 'Picture', 'sievert.png', 4, 4, 2, '', ''),
(74, 47, 0, 'Picture', 'reinzink.png', 5, 4, 2, '', ''),
(71, 47, 0, 'Picture', 'iko.png', 6, 4, 2, '', ''),
(72, 47, 0, 'Picture', 'Hilti.png', 1, 4, 2, '', ''),
(79, 48, 5, 'widget', '[{\"img\":\"home-head-top-kwaliteit-1.jpg\",\"text\":\"<h4 class=\\\"text-primary\\\" style=\\\"margin-top: 0px; margin-bottom: 0.5rem; font-family: Montserrat; font-weight: bold; line-height: 1.2; font-size: 1.5rem; text-align: center; color: navy !important;\\\">TOP KWALITEIT<\\/h4>\\n<p style=\\\"margin-bottom: 1rem; color: #000000; font-family: Lato; font-size: 16px; text-align: center;\\\">Wij werken alleen met de allerbeste materialen die voorzien zijn van uitgebreide garanties. Dat verhoogt aanzienlijk de levensduur van uw dak. Bij ons kunt u terecht voor het onderhoud van daken en goten. Net als een tuin, een huis of een auto heeft uw dak of dakgoot een jaarlijkse onderhoudsbeurt nodig. Onderhoud verlengt de levensduur van daken en goten. U voorkomt er erg grote en dure reparaties mee en investeert in de kwaliteit van uw huis of bedrijfspand.<\\/p>\"},{\"img\":\"home-head-zink-lood-werk.jpg\",\"text\":\"<h4 class=\\\"text-primary\\\" style=\\\"margin-top: 0px; margin-bottom: 0.5rem; font-family: Montserrat; font-weight: bold; line-height: 1.2; font-size: 1.5rem; text-align: center; color: navy !important;\\\">LOOD- EN ZINKWERK<\\/h4>\\n<p style=\\\"margin-bottom: 1rem; color: #000000; font-family: Lato; font-size: 16px; text-align: center;\\\">Goten en hemelwaterafvoeren van zink en koper zijn reeds jaren algemeen bekend. Maar ook complete daken en gevels worden uitgevoerd met lood, zink of koper. De bewezen levensduur van deze materialen in combinatie met een goede montage is indrukwekkend. Er zijn bijvoorbeeld dak- of gevelbekledingen die ouder zijn dan 500 jaar. Dit is een reden te meer om eens serieus over dergelijk systeem te gaan nadenken. Wij denken graag met U mee.<\\/p>\"},{\"img\":\"home-head-bitumineuze-dakbedekking.jpg\",\"text\":\"<h4 class=\\\"text-primary\\\" style=\\\"margin-top: 0px; margin-bottom: 0.5rem; font-family: Montserrat; font-weight: bold; line-height: 1.2; font-size: 1.5rem; text-align: center; color: navy !important;\\\">BITUMINEUZE DAKBEDEKKING<\\/h4>\\n<p style=\\\"margin-bottom: 1rem; color: #000000; font-family: Lato; font-size: 16px; text-align: center;\\\">Wij werken alleen met IKO producten, (voorheen Nebiprofa) dat sinds 1942 toonaangevend fabrikant van duurzame dakconcepten is. Dankzij onze jarenlange ervaring en brede assortiment dakbedekkingsmaterialen (IKO), isolatie en dakaccessoires bieden wij totaaloplossingen voor elk dak. Hierbij hebben wij oog voor duurzaamheid, gebruiksvriendelijkheid en kwaliteit van onze producten in het eindgebruik van onze dakconcepten.<\\/p>\"}]', 0, 12, 4, '', ''),
(80, 49, 0, 'TextArea', '<h1 style=\"text-align: center;\">Over ons</h1>\n<p style=\"text-align: center;\">Dakwerken John Geilen &ndash; derde generatie dakdekkers inmiddels &ndash; een bedrijf dat o.a. gespecialiseerd is in platte en gebitumineerde daken. Het aanleggen van lood &ndash; en zinkwerken is bij ons bedrijf in uitstekende handen. Tevens verzorgen wij reparaties aan pannendaken en voeren schoorsteenrenovaties uit.</p>\n<p style=\"text-align: center;\">&nbsp;</p>\n<p style=\"text-align: center;\">&nbsp;</p>', 0, 12, 2, '', ''),
(83, 49, 0, 'Picture', 'John-Geilen-Oud.png', 1, 4, 2, '', ''),
(92, 50, 0, 'TextArea', '<h1 style=\"text-align: center;\">Dakwerken John Geilen</h1>', 0, 12, 2, NULL, NULL),
(85, 49, 0, 'Picture', 'John-Geilen.png', 2, 4, 2, '', ''),
(86, 49, 0, 'Picture', 'logo-oud.png', 3, 4, 2, '', ''),
(89, 51, 0, 'TextArea', '<h3 style=\"text-align: center;\">Ons bedrijf</h3>\n<h1 style=\"text-align: center;\">In cijfers</h1>', 0, 12, 2, '', ''),
(90, 52, 0, 'TextArea', '<h1 style=\"text-align: center;\">Projecten</h1>', 0, 12, 2, '', ''),
(91, 53, 0, 'TextArea', '<h1 style=\"text-align: center;\">Contact</h1>\n<p>Wij zijn telefonisch bereikbaar van maandag t/m zaterdag van 8.00 tot 18.00 uur. Wij werken voor particulieren en voor bedrijven. Offertes worden kosteloos gemaakt en we berekenen geen voorrijkosten.</p>\n<p>&nbsp;</p>\n<p>06-53667219<br />046-737008<br />info@dwjg.nl<br />Verlengde Heinseweg 5<br />6136 AP Sittard</p>', 0, 12, 2, '', '');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblcmsgroups`
--

DROP TABLE IF EXISTS `tblcmsgroups`;
CREATE TABLE IF NOT EXISTS `tblcmsgroups` (
  `Group_Id` int(11) NOT NULL AUTO_INCREMENT,
  `Group_Page_Id` int(11) NOT NULL,
  `Group_Position` int(11) NOT NULL,
  `Group_Css_Class` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `Group_Css_Id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`Group_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Gegevens worden geëxporteerd voor tabel `tblcmsgroups`
--

INSERT INTO `tblcmsgroups` (`Group_Id`, `Group_Page_Id`, `Group_Position`, `Group_Css_Class`, `Group_Css_Id`) VALUES
(42, 10, -1, '', ''),
(43, 10, -1, '', ''),
(44, 10, -1, NULL, NULL),
(48, 11, 1, 'bg-primary', ''),
(47, 11, 4, '', 'partners'),
(49, 11, 2, 'bg-primary text-white', 'about'),
(50, 11, 0, '', ''),
(51, 11, 5, 'bg-primary text-white', ''),
(52, 11, 3, '', 'Projecten'),
(53, 11, 6, '', 'contact');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblcmspages`
--

DROP TABLE IF EXISTS `tblcmspages`;
CREATE TABLE IF NOT EXISTS `tblcmspages` (
  `Page_Id` int(11) NOT NULL AUTO_INCREMENT,
  `Page_Title` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `Page_Url` text COLLATE utf8_bin,
  `Page_Visible` tinyint(1) DEFAULT '0',
  `Page_Description` text COLLATE utf8_bin,
  PRIMARY KEY (`Page_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Gegevens worden geëxporteerd voor tabel `tblcmspages`
--

INSERT INTO `tblcmspages` (`Page_Id`, `Page_Title`, `Page_Url`, `Page_Visible`, `Page_Description`) VALUES
(11, 'test', '/', 0, NULL);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblrights`
--

DROP TABLE IF EXISTS `tblrights`;
CREATE TABLE IF NOT EXISTS `tblrights` (
  `Right_Id` int(11) NOT NULL,
  `Right_Name` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`Right_Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Gegevens worden geëxporteerd voor tabel `tblrights`
--

INSERT INTO `tblrights` (`Right_Id`, `Right_Name`) VALUES
(1, 'ARURA_USERS'),
(2, 'ARURA_ROLLES'),
(3, 'ARURA_SETTINGS'),
(10, 'CMS_PAGES'),
(11, 'CMS_MENU');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblrolerights`
--

DROP TABLE IF EXISTS `tblrolerights`;
CREATE TABLE IF NOT EXISTS `tblrolerights` (
  `RoleRight_Role_Id` int(11) NOT NULL,
  `RoleRight_Right_Id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Gegevens worden geëxporteerd voor tabel `tblrolerights`
--

INSERT INTO `tblrolerights` (`RoleRight_Role_Id`, `RoleRight_Right_Id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 11),
(1, 10),
(2, 10),
(2, 11);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblroles`
--

DROP TABLE IF EXISTS `tblroles`;
CREATE TABLE IF NOT EXISTS `tblroles` (
  `Role_Id` int(11) NOT NULL AUTO_INCREMENT,
  `Role_Name` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`Role_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Gegevens worden geëxporteerd voor tabel `tblroles`
--

INSERT INTO `tblroles` (`Role_Id`, `Role_Name`) VALUES
(1, 'Root'),
(2, 'Content Editor');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblsessions`
--

DROP TABLE IF EXISTS `tblsessions`;
CREATE TABLE IF NOT EXISTS `tblsessions` (
  `Session_Id` varchar(255) COLLATE utf8_bin NOT NULL,
  `Session_User_Id` int(11) NOT NULL,
  `Session_Last_Active` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Gegevens worden geëxporteerd voor tabel `tblsessions`
--

INSERT INTO `tblsessions` (`Session_Id`, `Session_User_Id`, `Session_Last_Active`) VALUES
('jbhm1565s6fm9l5q15mrbohfmt', 1, 1555964195);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblsettings`
--

DROP TABLE IF EXISTS `tblsettings`;
CREATE TABLE IF NOT EXISTS `tblsettings` (
  `Setting_Id` int(11) NOT NULL AUTO_INCREMENT,
  `Setting_Name` varchar(255) COLLATE utf8_bin NOT NULL,
  `Setting_Value` varchar(255) COLLATE utf8_bin NOT NULL,
  `Setting_Required` tinyint(1) NOT NULL,
  `Setting_Type` varchar(255) COLLATE utf8_bin NOT NULL,
  `Setting_Plg` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`Setting_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Gegevens worden geëxporteerd voor tabel `tblsettings`
--

INSERT INTO `tblsettings` (`Setting_Id`, `Setting_Name`, `Setting_Value`, `Setting_Required`, `Setting_Type`, `Setting_Plg`) VALUES
(1, 'reciever', 'nealgeilen@gmail.com', 0, 'email', 'plg.contact'),
(3, 'username', 'noreply.ng.server@gmail.com', 1, 'text', 'smtp'),
(4, 'server', 'smtp.gmail.com', 1, 'text', 'smtp'),
(5, 'password', '98sHdcMj5wG98I2VtHew', 1, 'password', 'smtp'),
(6, 'port', '587', 1, 'number', 'smtp'),
(7, 'Test', '', 0, 'text', 'smtp'),
(8, 'template', '/mail.html', 1, 'text', 'plg.contact');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tbluserroles`
--

DROP TABLE IF EXISTS `tbluserroles`;
CREATE TABLE IF NOT EXISTS `tbluserroles` (
  `UserRole_User_Id` int(11) NOT NULL,
  `UserRole_Role_Id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Gegevens worden geëxporteerd voor tabel `tbluserroles`
--

INSERT INTO `tbluserroles` (`UserRole_User_Id`, `UserRole_Role_Id`) VALUES
(1, 1),
(1, 2);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblusers`
--

DROP TABLE IF EXISTS `tblusers`;
CREATE TABLE IF NOT EXISTS `tblusers` (
  `User_Id` int(11) NOT NULL AUTO_INCREMENT,
  `User_Username` varchar(255) COLLATE utf8_bin NOT NULL,
  `User_Firstname` varchar(255) COLLATE utf8_bin NOT NULL,
  `User_Lastname` varchar(255) COLLATE utf8_bin NOT NULL,
  `User_Email` varchar(255) COLLATE utf8_bin NOT NULL,
  `User_Password` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`User_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Gegevens worden geëxporteerd voor tabel `tblusers`
--

INSERT INTO `tblusers` (`User_Id`, `User_Username`, `User_Firstname`, `User_Lastname`, `User_Email`, `User_Password`) VALUES
(1, 'nealgeilen@gmail.com', 'Neal', 'Geilen', 'nealgeilen@gmail.com', '$2y$10$kOV1Bi1bJeQMp.CzKi0yFOrbunNIRZVkQxCzRfm0BpQ49PZg329Du');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
