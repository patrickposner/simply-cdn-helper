��    T      �  q   \         4   !  �   V     �  T   �  A   F  A   �  >   �  _   		  $   i	     �	  i   �	  :   
  '   Q
     y
     �
     �
     �
     �
     �
     �
     �
     �
     �
                2     I     g  
   |     �     �     �     �  A   �            d   +     �     �     �     �     �     �  �   �     �  S   �  \   �     P     X     u     |     �  	   �     �  	   �  	   �     �     �     �     �            
   $     /     =     S     [  Y   q  G   �       J        j  �   z  �   "  K   �  }   	  V   �     �  	   �     �          '     *  �  .  1     �   =  !   �  U     W   g  a   �  V   !  g   x  $   �  $     {   *  V   �  1   �     /     @     N     V     f     j     v     �     �     �     �  "   �     �          5  
   L     W     k     |     �  B   �     �     �  h        q     �     �     �  
   �  %   �  �   �     �  d   �  ]        w  %   �     �  (   �  	   �  	   �     �  	         
          ,     2     F     T     f     x     �     �  	   �     �  f   �  R   6      �   F   �      �   �   �   �   �!  X   5"  �   �"  R   I#     �#     �#     �#     �#     �#     �#            6   0         #      <      T                  $   D       G   4   A          *                L   %   @       R   H      J   !   M          )      (           S          8   C              E   N      '   >   :   1       F   "         3      2      	   9   .   ?   ;       =   &      K       7           5   I           O   -             +   /         B       P      
   ,                                     Q    A little helper plugin to connect to simplystatic.io Activate the usage of static search. It uses the Algolia API and creates an complete index to search by title and content of each page. Add URL to Exclude Add the CSS selector of your search element here. The default value is .search-field Add the CSS selector which contains the content of the page/post. Add the CSS selector which contains the excerpt of the page/post. Add the CSS selector which contains the title of the page/post Add the site id that was sent to you by e-mail after you made your purchase on simplystatic.io. Add your Algolia Admin API Key here. Add your Algolia App ID here. Add your Algolia Search-Only API Key here. This is the only key that will be visible on your static site. Add your Algolia index name here. Default is simply_static Added the following URL to search index Additional URL Admin API Key Algolia Application ID CDN CDN (CNAME) CDN (Pull Zone) CDN (Storage Zone) CDN (Subdirectory) CSS-Selector CSS-Selector for Content CSS-Selector for Excerpt CSS-Selector for Title Configure your static website Connect your website Connection Connection Details Database (Host) Database (Password) Database (User) Decide whether or not you want to use search on your static site. E-Mail (from) Exclude URLs Exclude URLs from indexing. You can use full URLs, parts of an URL or plain words (like stop words). Generate Static Generate static Hosting Indexing Name (from) Name for your index Once your website is connected you can configure all settings related to the CDN here. This includes settings up redirects, proxy URLs and setting up a custom 404 error page. Patrick Posner Please add your SMTP credentials from your mail server if you want to send e-mails. Please connect your site to the simplystatic.io plattform to get access to your server data. Publish Pushed %d pages/files to CDN Region Relative path to your 404 page Remove SMTP Host SMTP Password SMTP Port SMTP User Save Changes Search Search-Only API Key Server (Host) Server (Password) Server (User) Setup SMTP Simply Static Simply Static Hosting Site-ID Static URL (optional) The native PHP Mailer is deactivated on the Simply Static plattform for security reasons. There was an connection error with Algolia. Please check your settings. Use search? Use this to generate a static version of the current page you are editing. View static URL Warning, you are currently running in development mode. Make sure to remove define( 'SSH_DEV_MODE', true ); from your wp-config.php before you run a new static export. When using CDN please make sure you are using relative URLs and that you have configured the necessary settings in Simply Static -> Settings -> Deployment You have to publish your post before you can create a static version of it. You need an active connection to get your access credentials to your server and to automatically deploy your site to the CDN. You need to add the static URL in your search settings before you can create an index. Your SSH-Key Your data https://patrickposner.dev https://simplystatic.io no yes Project-Id-Version: Simply Static Hosting
PO-Revision-Date: 2022-02-11 09:37+0100
Last-Translator: 
Language-Team: 
Language: de_DE
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=2; plural=(n != 1);
X-Generator: Poedit 3.0.1
X-Poedit-Basepath: ..
X-Poedit-Flags-xgettext: --add-comments=translators:
X-Poedit-WPHeader: simply-static-hosting.php
X-Poedit-SourceCharset: UTF-8
X-Poedit-KeywordsList: __;_e;_n:1,2;_x:1,2c;_ex:1,2c;_nx:4c,1,2;esc_attr__;esc_attr_e;esc_attr_x:1,2c;esc_html__;esc_html_e;esc_html_x:1,2c;_n_noop:1,2;_nx_noop:3c,1,2;__ngettext_noop:1,2
X-Poedit-SearchPath-0: .
X-Poedit-SearchPathExcluded-0: *.min.js
X-Poedit-SearchPathExcluded-1: vendor
 Ein Hilfsplugin zum Verbinden mit simplystatic.io Aktiviert die Suche auf deiner statischen Website. Es nutzt die Algolia API und erstellt einen kompletten Index in welchen nach Titel und Inhalt jeder Seite gesucht werden kann. URL zum Ausschließen hinzufügen Füge den CSS-Selektor für dein Suchfeld ein. Standardmäßig ist dies .search-field Füge den CSS-Selektor hinzu, welcher den Inhalt deines Beitrags/deiner Seite enthält. Füge den CSS-Selektor hinzu, welcher die Kurzbeschreibung deines Beitrags/deiner Seite enthält. Füge den CSS-Selektor hinzu, welcher den Titel deines Beitrags/deiner Seite enthält. Trage die Seiten-ID ein, welche dir via E-mail nach deinem Kauf auf simplystatic.io übermittelt wurde. Füge deinen Admin API Key hier ein. Füge deine Algolia App ID hier ein. Füge deinen Search-Only API Key hier ein. Diese ist der einzige Schlüssel der auf deiner statischen Website sichtbar ist. Füge hier den Namen deines Algolia-Index ein. Standardmäßig ist dies simply_static. Folgende URLs wurden zum Suche-Index hinzugefügt Zusätzliche URL Admin API Key Algolia Applikations-ID CDN CDN (CNAME) CDN (Pull Zone) CDN (Storage Zone) CDN (Unterverzeichnis) CSS-Selektor CSS-Selektor für Inhalt CSS-Selektor für Kurzbeschreibung CSS-Selektor für den Titel Statische Website konfigurieren Verbinde deine Website Verbindung Verbindungs-Details Datenbank (Host) Datenbank (Passwort) Database (Benutzer) Entscheide ob du die Such-Integration nutzen möchtest oder nicht. E-Mail (von) URLs ausschließen Schließe URLs vom Index aus. Du kannst vollständige URLs, Pfade oder Wörter benutzten (Stop-Wörter). Statisch generieren Statisch generieren Hosting Indexierung Name (von) Gebe einen Namen für deinen Index an Sobald deine Website mit der Plattform verbunden ist, kannst du hier deine CDN-Einstellungen überarbeiten. Setze Weiterleitungen, richte Proxy-URLs ein und konfiguriere eine eigene 404-Fehlerseite. Patrick Posner Bitte füge deine SMTP-Zugangsdaten deines E-Mail-Servers hinzu wenn du E-Mails versenden möchtest. Bitte verbinde deine Website mit simplystatic.io um Zugang zu deinen Serverdaten zu erhalten. Veröffentlichen  %d Seiten/Dateien zu CDN übertragen Region Relativer Pfad zu deiner 404-Fehlerseite Entfernen SMTP Host SMTP Passwort SMTP Port SMTP Nutzer Änderungen speichern Suche Search-Only API Key Server (Host) Server (Passwort) Server (Benutzer) SMTP einrichten Simply Static Simply Static Hosting Seiten-ID Statische URL (optional) Der native PHP-Mailer ist auf der Simply Static Hosting Plattform aus Sicherheitsgründen deaktiviert. Es gab einen Verbindungsfehler mit Algolia. Bitte überprüfe deine Einstellungen. Suche benutzen? Nutze dies um eine statische Version der aktuellen Seite zu erstellen. Statische URL ansehen Achtung, du bist aktuell im Entwicklungsmodus. Stelle sicher, dass du define( 'SSH_DEV_MODE', true ); aus deiner wp-config.php entfernst, bevor du einen neuen statischen Export startest. Bei Benutzung des CDNs stelle sicher, dass du relative URLs benutzt. Dies kannst du in Simply Static - Einstellungen konfigurieren. Du musst deinen Beitrag erst veröffentlichen, bevor du ihn statisch exportieren kannst. Du benötigst eine aktive Verbindung um auf deine Serverdaten wie SFTP, Datenbank usw. zugreifen zu können. Auch die Übertragung aufs CDN ist nur mit einer aktiven Verbindung möglich. Du musst die statische URL hinzufügen, damit du einen Suchindex erstellen kannst. Dein SSH-Schlüssel Deine Daten https://patrickposner.dev https://simplystatic.io nein ja 