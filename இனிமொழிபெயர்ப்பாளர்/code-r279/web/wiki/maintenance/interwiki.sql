-- Based more or less on the public interwiki map from MeatballWiki
-- Default interwiki prefixes...

REPLACE INTO /*$wgDBprefix*/interwiki (iw_prefix,iw_url,iw_local) VALUES
('abbenormal','http://www.ourpla.net/cgi-bin/pikie.cgi?$1',0),
('acadwiki','http://xarch.tu-graz.ac.at/autocad/wiki/$1',0),
('acronym','http://www.acronymfinder.com/af-query.asp?String=exact&Acronym=$1',0),
('advogato','http://www.advogato.org/$1',0),
('aiwiki','http://www.ifi.unizh.ch/ailab/aiwiki/aiw.cgi?$1',0),
('alife','http://news.alife.org/wiki/index.php?$1',0),
('annotation','http://bayle.stanford.edu/crit/nph-med.cgi/$1',0),
('annotationwiki','http://www.seedwiki.com/page.cfm?wikiid=368&doc=$1',0),
('arxiv','http://www.arxiv.org/abs/$1',0),
('aspienetwiki','http://aspie.mela.de/Wiki/index.php?title=$1',0),
('bemi','http://bemi.free.fr/vikio/index.php?$1',0),
('benefitswiki','http://www.benefitslink.com/cgi-bin/wiki.cgi?$1',0),
('brasilwiki','http://rio.ifi.unizh.ch/brasilienwiki/index.php/$1',0),
('bridgeswiki','http://c2.com/w2/bridges/$1',0),
('c2find','http://c2.com/cgi/wiki?FindPage&value=$1',0),
('cache','http://www.google.com/search?q=cache:$1',0),
('ciscavate','http://ciscavate.org/index.php/$1',0),
('cliki','http://ww.telent.net/cliki/$1',0),
('cmwiki','http://www.ourpla.net/cgi-bin/wiki.pl?$1',0),
('codersbase','http://www.codersbase.com/$1',0),
('commons','http://commons.wikimedia.org/wiki/$1',0),
('consciousness','http://teadvus.inspiral.org/',0),
('corpknowpedia','http://corpknowpedia.org/wiki/index.php/$1',0),
('creationmatters','http://www.ourpla.net/cgi-bin/wiki.pl?$1',0),
('dejanews','http://www.deja.com/=dnc/getdoc.xp?AN=$1',0),
('demokraatia','http://wiki.demokraatia.ee/',0),
('dictionary','http://www.dict.org/bin/Dict?Database=*&Form=Dict1&Strategy=*&Query=$1',0),
('disinfopedia','http://www.disinfopedia.org/wiki.phtml?title=$1',0),
('diveintoosx','http://diveintoosx.org/$1',0),
('docbook','http://docbook.org/wiki/moin.cgi/$1',0),
('dolphinwiki','http://www.object-arts.com/wiki/html/Dolphin/$1',0),
('drumcorpswiki','http://www.drumcorpswiki.com/index.php/$1',0),
('dwjwiki','http://www.suberic.net/cgi-bin/dwj/wiki.cgi?$1',0),
('e??ei','http://www.ikso.net/cgi-bin/wiki.pl?$1',0),
('echei','http://www.ikso.net/cgi-bin/wiki.pl?$1',0),
('ecxei','http://www.ikso.net/cgi-bin/wiki.pl?$1',0),
('efnetceewiki','http://purl.net/wiki/c/$1',0),
('efnetcppwiki','http://purl.net/wiki/cpp/$1',0),
('efnetpythonwiki','http://purl.net/wiki/python/$1',0),
('efnetxmlwiki','http://purl.net/wiki/xml/$1',0),
('eljwiki','http://elj.sourceforge.net/phpwiki/index.php/$1',0),
('emacswiki','http://www.emacswiki.org/cgi-bin/wiki.pl?$1',0),
('elibre','http://enciclopedia.us.es/index.php/$1',0),
('eokulturcentro','http://esperanto.toulouse.free.fr/wakka.php?wiki=$1',0),
('evowiki','http://www.evowiki.org/index.php/$1',0),
('finalempire','http://final-empire.sourceforge.net/cgi-bin/wiki.pl?$1',0),
('firstwiki','http://firstwiki.org/index.php/$1',0),
('foldoc','http://www.foldoc.org/foldoc/foldoc.cgi?$1',0),
('foxwiki','http://fox.wikis.com/wc.dll?Wiki~$1',0),
('fr.be','http://fr.wikinations.be/$1',0),
('fr.ca','http://fr.ca.wikinations.org/$1',0),
('fr.fr','http://fr.fr.wikinations.org/$1',0),
('fr.org','http://fr.wikinations.org/$1',0),
('freebsdman','http://www.FreeBSD.org/cgi/man.cgi?apropos=1&query=$1',0),
('gamewiki','http://gamewiki.org/wiki/index.php/$1',0),
('gej','http://www.esperanto.de/cgi-bin/aktivikio/wiki.pl?$1',0),
('gentoo-wiki','http://gentoo-wiki.com/$1',0),
('globalvoices','http://cyber.law.harvard.edu/dyn/globalvoices/wiki/$1',0),
('gmailwiki','http://www.gmailwiki.com/index.php/$1',0),
('google','http://www.google.com/search?q=$1',0),
('googlegroups','http://groups.google.com/groups?q=$1',0),
('gotamac','http://www.got-a-mac.org/$1',0),
('greencheese','http://www.greencheese.org/$1',0),
('hammondwiki','http://www.dairiki.org/HammondWiki/index.php3?$1',0),
('haribeau','http://wiki.haribeau.de/cgi-bin/wiki.pl?$1',0),
('hewikisource','http://he.wikisource.org/wiki/$1',1),
('herzkinderwiki','http://www.herzkinderinfo.de/Mediawiki/index.php/$1',0),
('hrwiki','http://www.hrwiki.org/index.php/$1',0),
('iawiki','http://www.IAwiki.net/$1',0),
('imdb','http://us.imdb.com/Title?$1',0),
('infosecpedia','http://www.infosecpedia.org/pedia/index.php/$1',0),
('jargonfile','http://sunir.org/apps/meta.pl?wiki=JargonFile&redirect=$1',0),
('jefo','http://www.esperanto-jeunes.org/vikio/index.php?$1',0),
('jiniwiki','http://www.cdegroot.com/cgi-bin/jini?$1',0),
('jspwiki','http://www.ecyrd.com/JSPWiki/Wiki.jsp?page=$1',0),
('kerimwiki','http://wiki.oxus.net/$1',0),
('kmwiki','http://www.voght.com/cgi-bin/pywiki?$1',0),
('knowhow','http://www2.iro.umontreal.ca/~paquetse/cgi-bin/wiki.cgi?$1',0),
('lanifexwiki','http://opt.lanifex.com/cgi-bin/wiki.pl?$1',0),
('lasvegaswiki','http://wiki.gmnow.com/index.php/$1',0),
('linuxwiki','http://www.linuxwiki.de/$1',0),
('lojban','http://www.lojban.org/tiki/tiki-index.php?page=$1',0),
('lqwiki','http://wiki.linuxquestions.org/wiki/$1',0),
('lugkr','http://lug-kr.sourceforge.net/cgi-bin/lugwiki.pl?$1',0),
('lutherwiki','http://www.lutheranarchives.com/mw/index.php/$1',0),
('mathsongswiki','http://SeedWiki.com/page.cfm?wikiid=237&doc=$1',0),
('mbtest','http://www.usemod.com/cgi-bin/mbtest.pl?$1',0),
('meatball','http://www.usemod.com/cgi-bin/mb.pl?$1',0),
('mediazilla','http://bugzilla.wikipedia.org/$1',1),
('memoryalpha','http://www.memory-alpha.org/en/index.php/$1',0),
('metaweb','http://www.metaweb.com/wiki/wiki.phtml?title=$1',0),
('metawiki','http://sunir.org/apps/meta.pl?$1',0),
('metawikipedia','http://meta.wikimedia.org/wiki/$1',0),
('moinmoin','http://purl.net/wiki/moin/$1',0),
('mozillawiki','http://wiki.mozilla.org/index.php/$1',0),
('muweb','http://www.dunstable.com/scripts/MuWebWeb?$1',0),
('netvillage','http://www.netbros.com/?$1',0),
('oeis','http://www.research.att.com/cgi-bin/access.cgi/as/njas/sequences/eisA.cgi?Anum=$1',0),
('openfacts','http://openfacts.berlios.de/index.phtml?title=$1',0),
('openwiki','http://openwiki.com/?$1',0),
('opera7wiki','http://nontroppo.org/wiki/$1',0),
('orgpatterns','http://www.bell-labs.com/cgi-user/OrgPatterns/OrgPatterns?$1',0),
('osi reference model','http://wiki.tigma.ee/',0),
('pangalacticorg','http://www.pangalactic.org/Wiki/$1',0),
('personaltelco','http://www.personaltelco.net/index.cgi/$1',0),
('patwiki','http://gauss.ffii.org/$1',0),
('phpwiki','http://phpwiki.sourceforge.net/phpwiki/index.php?$1',0),
('pikie','http://pikie.darktech.org/cgi/pikie?$1',0),
('pmeg','http://www.bertilow.com/pmeg/$1.php',0),
('ppr','http://c2.com/cgi/wiki?$1',0),
('purlnet','http://purl.oclc.org/NET/$1',0),
('pythoninfo','http://www.python.org/cgi-bin/moinmoin/$1',0),
('pythonwiki','http://www.pythonwiki.de/$1',0),
('pywiki','http://www.voght.com/cgi-bin/pywiki?$1',0),
('raec','http://www.raec.clacso.edu.ar:8080/raec/Members/raecpedia/$1',0),
('revo','http://purl.org/NET/voko/revo/art/$1.html',0),
('rfc','http://www.rfc-editor.org/rfc/rfc$1.txt',0),
('s23wiki','http://is-root.de/wiki/index.php/$1',0),
('scoutpedia','http://www.scoutpedia.info/index.php/$1',0),
('seapig','http://www.seapig.org/$1',0),
('seattlewiki','http://seattlewiki.org/wiki/$1',0),
('seattlewireless','http://seattlewireless.net/?$1',0),
('seeds','http://www.IslandSeeds.org/wiki/$1',0),
('senseislibrary','http://senseis.xmp.net/?$1',0),
('shakti','http://cgi.algonet.se/htbin/cgiwrap/pgd/ShaktiWiki/$1',0),
('slashdot','http://slashdot.org/article.pl?sid=$1',0),
('smikipedia','http://www.smikipedia.org/$1',0),
('sockwiki','http://wiki.socklabs.com/$1',0),
('sourceforge','http://sourceforge.net/$1',0),
('squeak','http://minnow.cc.gatech.edu/squeak/$1',0),
('strikiwiki','http://ch.twi.tudelft.nl/~mostert/striki/teststriki.pl?$1',0),
('susning','http://www.susning.nu/$1',0),
('svgwiki','http://www.protocol7.com/svg-wiki/default.asp?$1',0),
('tavi','http://tavi.sourceforge.net/$1',0),
('tejo','http://www.tejo.org/vikio/$1',0),
('terrorwiki','http://www.liberalsagainstterrorism.com/wiki/index.php/$1',0),
('tmbw','http://www.tmbw.net/wiki/index.php/$1',0),
('tmnet','http://www.technomanifestos.net/?$1',0),
('tmwiki','http://www.EasyTopicMaps.com/?page=$1',0),
('turismo','http://www.tejo.org/turismo/$1',0),
('theopedia','http://www.theopedia.com/$1',0),
('twiki','http://twiki.org/cgi-bin/view/$1',0),
('twistedwiki','http://purl.net/wiki/twisted/$1',0),
('uea','http://www.tejo.org/uea/$1',0),
('unreal','http://wiki.beyondunreal.com/wiki/$1',0),
('ursine','http://ursine.ca/$1',0),
('usej','http://www.tejo.org/usej/$1',0),
('usemod','http://www.usemod.com/cgi-bin/wiki.pl?$1',0),
('visualworks','http://wiki.cs.uiuc.edu/VisualWorks/$1',0),
('warpedview','http://www.warpedview.com/index.php/$1',0),
('webdevwikinl','http://www.promo-it.nl/WebDevWiki/index.php?page=$1',0),
('webisodes','http://www.webisodes.org/$1',0),
('webseitzwiki','http://webseitz.fluxent.com/wiki/$1',0),
('why','http://clublet.com/c/c/why?$1',0),
('wiki','http://c2.com/cgi/wiki?$1',0),
('wikia','http://www.wikia.com/wiki/index.php/$1',0),
('wikibooks','http://en.wikibooks.org/wiki/$1',1),
('wikicities','http://www.wikicities.com/index.php/$1',0),
('wikif1','http://www.wikif1.org/$1',0),
('wikinfo','http://www.wikinfo.org/wiki.php?title=$1',0),
('wikimedia','http://wikimediafoundation.org/wiki/$1',0),
('wikiquote','http://en.wikiquote.org/wiki/$1',1),
('wikinews','http://en.wikinews.org/wiki/$1',0),
('wikisource','http://sources.wikipedia.org/wiki/$1',1),
('wikispecies','http://species.wikipedia.org/wiki/$1',1),
('wikitravel','http://wikitravel.org/en/$1',0),
('wikiworld','http://WikiWorld.com/wiki/index.php/$1',0),
('wiktionary','http://en.wiktionary.org/wiki/$1',1),
('wlug','http://www.wlug.org.nz/$1',0),
('wlwiki','http://winslowslair.supremepixels.net/wiki/index.php/$1',0),
('ypsieyeball','http://sknkwrks.dyndns.org:1957/writewiki/wiki.pl?$1',0),
('zwiki','http://www.zwiki.org/$1',0),
('zzz wiki','http://wiki.zzz.ee/',0),
('wikt','http://en.wiktionary.org/wiki/$1',1);

