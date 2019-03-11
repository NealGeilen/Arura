-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Gegenereerd op: 11 mrt 2019 om 20:55
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
  PRIMARY KEY (`Content_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Gegevens worden geëxporteerd voor tabel `tblcmscontentblocks`
--

INSERT INTO `tblcmscontentblocks` (`Content_Id`, `Content_Group_Id`, `Content_Addon_Id`, `Content_Type`, `Content_Value`, `Content_Position`, `Content_Size`, `Content_Raster`) VALUES
(23, 37, 0, 'TextArea', '<h2 style=\"text-align: center;\">Over Ons</h2>\n<p style=\"text-align: center;\">Dakwerken John Geilen &ndash; derde generatie dakdekkers inmiddels &ndash; een bedrijf dat o.a. gespecialiseerd is in platte en gebitumineerde daken. Het aanleggen van lood &ndash; en zinkwerken is bij ons bedrijf in uitstekende handen. Tevens verzorgen wij reparaties aan pannendaken en voeren schoorsteenrenovaties uit.</p>', 0, 12, 2),
(16, 31, 5, 'widget', '[{\"img\":\"\\/assets\\/img\\/home-head-top-kwaliteit-1.jpg\",\"text\":\"<h4 class=\\\"text-primary\\\" style=\\\"text-align: center;\\\">TOP KWALITEIT<\\/h4>\\n<p style=\\\"text-align: center;\\\">Wij werken alleen met de allerbeste materialen die voorzien zijn van uitgebreide garanties. Dat verhoogt aanzienlijk de levensduur van uw dak. Bij ons kunt u terecht voor het onderhoud van daken en goten. Net als een tuin, een huis of een auto heeft uw dak of dakgoot een jaarlijkse onderhoudsbeurt nodig. Onderhoud verlengt de levensduur van daken en goten. U voorkomt er erg grote en dure reparaties mee en investeert in de kwaliteit van uw huis of bedrijfspand.<\\/p>\"},{\"img\":\"\\/assets\\/img\\/home-head-zink-lood-werk.jpg\",\"text\":\"<h4 class=\\\"text-primary\\\" style=\\\"text-align: center;\\\">LOOD- EN ZINKWERK<\\/h4>\\n<p style=\\\"text-align: center;\\\">Goten en hemelwaterafvoeren van zink en koper zijn reeds jaren algemeen bekend. Maar ook complete daken en gevels worden uitgevoerd met lood, zink of koper. De bewezen levensduur van deze materialen in combinatie met een goede montage is indrukwekkend. Er zijn bijvoorbeeld dak- of gevelbekledingen die ouder zijn dan 500 jaar. Dit is een reden te meer om eens serieus over dergelijk systeem te gaan nadenken. Wij denken graag met U mee.<\\/p>\"},{\"img\":\"\\/assets\\/img\\/home-head-bitumineuze-dakbedekking.jpg\",\"text\":\"<h4 class=\\\"text-primary\\\" style=\\\"text-align: center;\\\">BITUMINEUZE DAKBEDEKKING<\\/h4>\\n<p style=\\\"text-align: center;\\\">Wij werken alleen met IKO producten, (voorheen Nebiprofa) dat sinds 1942 toonaangevend fabrikant van duurzame dakconcepten is. Dankzij onze jarenlange ervaring en brede assortiment dakbedekkingsmaterialen (IKO), isolatie en dakaccessoires bieden wij totaaloplossingen voor elk dak. Hierbij hebben wij oog voor duurzaamheid, gebruiksvriendelijkheid en kwaliteit van onze producten in het eindgebruik van onze dakconcepten.<\\/p>\"}]', 0, 12, 4),
(22, 36, 0, 'TextArea', '<h1 style=\"text-align: center;\">Dakwerken John Geilen</h1>', 0, 12, 2),
(24, 37, 0, 'Picture', '/assets/img/john_geiler.jpg', 2, 4, 2),
(26, 37, 0, 'Picture', '/assets/img/john_geilen_history.jpg', 1, 4, 2),
(27, 37, 0, 'Picture', '/assets/img/john_geilen_history_logo.jpg', 3, 4, 2),
(28, 38, 0, 'TextArea', '<h1 style=\"text-align: center;\">Onze partners</h1>', 0, 12, 2),
(29, 38, 0, 'Picture', '/assets/img/vmzinc-logo.png', 3, 4, 2),
(34, 38, 0, 'Picture', '/assets/img/Iko-logo.jpg', 4, 4, 2),
(35, 38, 0, 'Picture', '/assets/img/Sievert-logo.jpg', 5, 4, 2),
(37, 38, 0, 'Picture', '/assets/img/Velux-logo.jpg', 1, 4, 2),
(38, 38, 0, 'Picture', '/assets/img/Hilti-logo.jpg', 2, 4, 2),
(39, 38, 0, 'Picture', '/assets/img/Rheinzink-logo.jpg', 6, 4, 2);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblcmsgroups`
--

DROP TABLE IF EXISTS `tblcmsgroups`;
CREATE TABLE IF NOT EXISTS `tblcmsgroups` (
  `Group_Id` int(11) NOT NULL AUTO_INCREMENT,
  `Group_Page_Id` int(11) NOT NULL,
  `Group_Position` int(11) NOT NULL,
  `Group_Css_Class` varchar(255) COLLATE utf8_bin NOT NULL,
  `Group_Css_Id` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`Group_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Gegevens worden geëxporteerd voor tabel `tblcmsgroups`
--

INSERT INTO `tblcmsgroups` (`Group_Id`, `Group_Page_Id`, `Group_Position`, `Group_Css_Class`, `Group_Css_Id`) VALUES
(37, 1, 2, 'bg-primary text-secondary', ''),
(31, 1, 1, 'bg-primary ', ''),
(36, 1, 0, 'bg-primary text-secondary', ''),
(38, 1, 3, '', '');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblcmsnavbaritems`
--

DROP TABLE IF EXISTS `tblcmsnavbaritems`;
CREATE TABLE IF NOT EXISTS `tblcmsnavbaritems` (
  `NavbarItem_Id` int(11) NOT NULL AUTO_INCREMENT,
  `NavbarItem_Title` varchar(255) COLLATE utf8_bin NOT NULL,
  `NavbarItem_Url` longtext COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`NavbarItem_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Gegevens worden geëxporteerd voor tabel `tblcmsnavbaritems`
--

INSERT INTO `tblcmsnavbaritems` (`NavbarItem_Id`, `NavbarItem_Title`, `NavbarItem_Url`) VALUES
(1, 'Over ons', '#aboutus'),
(2, 'Projecten', '#projects'),
(3, 'Onze partners', '#partners'),
(4, 'Contact', '#contact');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tblcmspages`
--

DROP TABLE IF EXISTS `tblcmspages`;
CREATE TABLE IF NOT EXISTS `tblcmspages` (
  `Page_Id` int(11) NOT NULL AUTO_INCREMENT,
  `Page_Title` varchar(255) COLLATE utf8_bin NOT NULL,
  `Page_Url` text COLLATE utf8_bin,
  `Page_Visible` tinyint(1) DEFAULT '0',
  `Page_Description` text COLLATE utf8_bin,
  PRIMARY KEY (`Page_Id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Gegevens worden geëxporteerd voor tabel `tblcmspages`
--

INSERT INTO `tblcmspages` (`Page_Id`, `Page_Title`, `Page_Url`, `Page_Visible`, `Page_Description`) VALUES
(1, 'index', '/', 1, 'awdwakduwakldhawjkldhwakljdhawdkjwhadkjwahdkjawhdkjwahdkawjdhawkjdhawkjldhawkjhkdhawdklwad');

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
  `Role_Id` int(11) NOT NULL,
  `Right_Id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Gegevens worden geëxporteerd voor tabel `tblrolerights`
--

INSERT INTO `tblrolerights` (`Role_Id`, `Right_Id`) VALUES
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
('m128qn3ggp43cdoij76o8qjrqc', 1, 1551947410),
('19qrhnpulhtf8onpd56qgobns5', 1, 1551784792),
('crgg8ekibcn3t6unl5lm0htpm7', 1, 1551111987),
('v554mvktb8cemera96pd6b3oqe', 1, 1551720019),
('cncivejdjtmjrhd8fepjdjsab7', 1, 1551536355),
('8n2ho56n6i1t5rmfhv23heumj1', 1, 1552334614);

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
(7, 'secure', '', 0, 'text', 'smtp'),
(8, 'template', '/mail.html', 1, 'text', 'plg.contact');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tbluserroles`
--

DROP TABLE IF EXISTS `tbluserroles`;
CREATE TABLE IF NOT EXISTS `tbluserroles` (
  `User_Id` int(11) NOT NULL,
  `Role_Id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Gegevens worden geëxporteerd voor tabel `tbluserroles`
--

INSERT INTO `tbluserroles` (`User_Id`, `Role_Id`) VALUES
(1, 1);

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
