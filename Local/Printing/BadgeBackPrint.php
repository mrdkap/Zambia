%!PS-Adobe-3.0

/deffont {
  findfont exch scalefont def
} bind def

/reencode_font {
  findfont reencode 2 copy definefont pop def
} bind def

% reencode the font
% <encoding-vector> <fontdict> -> <newfontdict>
/reencode { %def
  dup length 5 add dict begin
    { %forall
      1 index /FID ne
      { def }{ pop pop } ifelse
    } forall
    /Encoding exch def

    % Use the font's bounding box to determine the ascent, descent,
    % and overall height; don't forget that these values have to be
    % transformed using the font's matrix.
    % We use 'load' because sometimes BBox is executable, sometimes not.
    % Since we need 4 numbers an not an array avoid BBox from being executed
    /FontBBox load aload pop
    FontMatrix transform /Ascent exch def pop
    FontMatrix transform /Descent exch def pop
    /FontHeight Ascent Descent sub def

    % Define these in case they're not in the FontInfo (also, here
    % they're easier to get to.
    /UnderlinePosition 1 def
    /UnderlineThickness 1 def

    % Get the underline position and thickness if they're defined.
    currentdict /FontInfo known {
      FontInfo

      dup /UnderlinePosition known {
        dup /UnderlinePosition get
        0 exch FontMatrix transform exch pop
        /UnderlinePosition exch def
      } if

      dup /UnderlineThickness known {
        /UnderlineThickness get
        0 exch FontMatrix transform exch pop
        /UnderlineThickness exch def
      } if

    } if
    currentdict
  end
} bind def

/ISO-8859-1Encoding [
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/space /exclam /quotedbl /numbersign /dollar /percent /ampersand /quoteright
/parenleft /parenright /asterisk /plus /comma /minus /period /slash
/zero /one /two /three /four /five /six /seven
/eight /nine /colon /semicolon /less /equal /greater /question
/at /A /B /C /D /E /F /G
/H /I /J /K /L /M /N /O
/P /Q /R /S /T /U /V /W
/X /Y /Z /bracketleft /backslash /bracketright /asciicircum /underscore
/quoteleft /a /b /c /d /e /f /g
/h /i /j /k /l /m /n /o
/p /q /r /s /t /u /v /w
/x /y /z /braceleft /bar /braceright /asciitilde /.notdef
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/space /exclamdown /cent /sterling /currency /yen /brokenbar /section
/dieresis /copyright /ordfeminine /guillemotleft /logicalnot /hyphen /registered /macron
/degree /plusminus /twosuperior /threesuperior /acute /mu /paragraph /bullet
/cedilla /onesuperior /ordmasculine /guillemotright /onequarter /onehalf /threequarters /questiondown
/Agrave /Aacute /Acircumflex /Atilde /Adieresis /Aring /AE /Ccedilla
/Egrave /Eacute /Ecircumflex /Edieresis /Igrave /Iacute /Icircumflex /Idieresis
/Eth /Ntilde /Ograve /Oacute /Ocircumflex /Otilde /Odieresis /multiply
/Oslash /Ugrave /Uacute /Ucircumflex /Udieresis /Yacute /Thorn /germandbls
/agrave /aacute /acircumflex /atilde /adieresis /aring /ae /ccedilla
/egrave /eacute /ecircumflex /edieresis /igrave /iacute /icircumflex /idieresis
/eth /ntilde /ograve /oacute /ocircumflex /otilde /odieresis /divide
/oslash /ugrave /uacute /ucircumflex /udieresis /yacute /thorn /ydieresis
] def
/ISOArial ISO-8859-1Encoding /Arial reencode_font
/labelclip {
	newpath
	1.000000 1.000000 moveto
	251.000000 1.000000 lineto
	251.000000 159.000000 lineto
	1.000000 159.000000 lineto
	closepath
	clip

} def

% end prologue

% set font type and size
ISOArial 16 scalefont setfont
%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Abby ) show
6 132 moveto
( Doors and Floors - 1pm Sat ) show
9 124 moveto
( Sat 1:00 PM - 2hr - Doors-Floors ) show
6 116 moveto
( Doors and Floors - 12pm Sun ) show
9 108 moveto
( Sun 12:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( AbbyF ) show
6 132 moveto
( Load In: Team 3 - 10am Fri ) show
9 124 moveto
( Fri 10:00 AM - 2hr - Loading Dock - Team 3 ) show
6 116 moveto
( Load Out: Team 4 - 4pm Sun ) show
9 108 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 4 ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Abelee ) show
6 132 moveto
( NELA Sales Table - 8pm Fri ) show
9 124 moveto
( Fri 8:00 PM - 2hr - NELA Sales ) show
6 116 moveto
( NELA Sales Table - 10am Sat ) show
9 108 moveto
( Sat 10:00 AM - 2hr - NELA Sales ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Addie ) show
6 132 moveto
( NELA Sales Table - 9am Sun ) show
9 124 moveto
( Sun 9:00 AM - 2hr - NELA Sales ) show
6 116 moveto
( NELA Sales Table - 11am Sun ) show
9 108 moveto
( Sun 11:00 AM - 2hr - NELA Sales ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Alec ) show
6 132 moveto
( ID Checker - 6pm Fri ) show
9 124 moveto
( Fri 6:00 PM - 2hr - Reg Desk-ID ) show
6 116 moveto
( Wristbander - 8pm Fri ) show
9 108 moveto
( Fri 8:00 PM - 2hr - Reg Desk ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Alex M ) show
6 132 moveto
( Volunteer Lounge 1pm Sat ) show
9 124 moveto
( Sat 1:00 PM - 2hr - Boardroom ) show
6 116 moveto
( Doors and Floors - 5pm Sat ) show
9 108 moveto
( Sat 5:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Alexis Mae ) show
6 132 moveto
( Load In: Team 5 - 6pm Fri ) show
9 124 moveto
( Fri 6:00 PM - 2hr - Loading Dock - Team 5 ) show
6 116 moveto
( Doors and Floors - 9am Sat ) show
9 108 moveto
( Sat 9:00 AM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Amanda ) show
6 132 moveto
( Wristbander - 6pm Fri ) show
9 124 moveto
( Fri 6:00 PM - 2hr - Reg Desk ) show
6 116 moveto
( ID Checker - 8pm Fri ) show
9 108 moveto
( Fri 8:00 PM - 2hr - Reg Desk-ID ) show
6 100 moveto
( Wristbander - 9am Sat ) show
9 92 moveto
( Sat 9:00 AM - 1hr - Reg Desk ) show
6 84 moveto
( Wristbander - 12pm Sat ) show
9 76 moveto
( Sat 12:00 PM - 2hr - Reg Desk ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Ambra Storm ) show
6 132 moveto
( Load In: Team 6 - 10am Fri ) show
9 124 moveto
( Fri 10:00 AM - 2hr - Loading Dock - Team 6 ) show
6 116 moveto
( Load In: Team 6 - 12pm Fri ) show
9 108 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 6 ) show
6 100 moveto
( Load In: Team 6 - 2pm Fri ) show
9 92 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 6 ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Andrew ) show
6 132 moveto
( Doors and Floors - 3pm Sat ) show
9 124 moveto
( Sat 3:00 PM - 2hr - Doors-Floors ) show
6 116 moveto
( Doors and Floors - 5pm Sat ) show
9 108 moveto
( Sat 5:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Andrew M ) show
6 132 moveto
( Volunteer Lounge 9am Sun ) show
9 124 moveto
( Sun 9:00 AM - 2hr - Boardroom ) show
6 116 moveto
( Doors and Floors - 12pm Sun ) show
9 108 moveto
( Sun 12:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Angel ) show
6 132 moveto
( Load In: Team 2 - 12pm Fri ) show
9 124 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 2 ) show
6 116 moveto
( Load In: Team 2 - 2pm Fri ) show
9 108 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 2 ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Ann ) show
6 132 moveto
( ID Checker - 9am Sat ) show
9 124 moveto
( Sat 9:00 AM - 1hr - Reg Desk-ID ) show
6 116 moveto
( Volunteer Lounge 11am Sat ) show
9 108 moveto
( Sat 11:00 AM - 2hr - Boardroom ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Ariel ) show
6 132 moveto
( Load In: Team 6 - 2pm Fri ) show
9 124 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 6 ) show
6 116 moveto
( Load In: Team 6 - 4pm Fri ) show
9 108 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 6 ) show
6 100 moveto
( Load Out: Team 1 - 4pm Sun ) show
9 92 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 1 ) show
6 84 moveto
( Load Out: Team 2 - 6pm Sun ) show
9 76 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 2 ) show
6 68 moveto
( Load Out: Team 1 - 8pm Sun ) show
9 60 moveto
( Sun 8:00 PM - 2hr - Loading Dock - Team 1 ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Ashley ) show
6 132 moveto
( Load In: Team 5 - 12pm Fri ) show
9 124 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 5 ) show
6 116 moveto
( Load In: Team 5 - 2pm Fri ) show
9 108 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 5 ) show
6 100 moveto
( Load In: Team 5 - 4pm Fri ) show
9 92 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 5 ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( AspiringWordsmith ) show
6 132 moveto
( Load In: Team 3 - 2pm Fri ) show
9 124 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 3 ) show
6 116 moveto
( Load In: Team 3 - 4pm Fri ) show
9 108 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 3 ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Athair ) show
6 132 moveto
( ID Checker - 10am Sat ) show
9 124 moveto
( Sat 10:00 AM - 2hr - Reg Desk-ID ) show
6 116 moveto
( 4th Floor - 3pm Sat ) show
9 108 moveto
( Sat 3:00 PM - 2hr - CM-4th Floor ) show
6 100 moveto
( Wristbander - 11am Sun ) show
9 92 moveto
( Sun 11:00 AM - 2hr - Reg Desk ) show
6 84 moveto
( Doors and Floors - 2pm Sun ) show
9 76 moveto
( Sun 2:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( AthenaKali ) show
6 132 moveto
( Elemental One Hand Clap (announcer/inside room attendant) ) show
9 124 moveto
( Sat 2:45 PM - 1hr 30min - 518 ) show
6 116 moveto
( Fucking With Your Camera (announcer/inside room attendant) ) show
9 108 moveto
( Sat 4:30 PM - 1hr 30min - 418 ) show
6 100 moveto
( The Lazy Top's Guide to Pacing a Scene  (announcer/inside room attendant) ) show
9 92 moveto
( Sun 1:45 PM - 1hr 30min - Naragansett A ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Autumn ) show
6 132 moveto
( Wristbander - 11am Sun ) show
9 124 moveto
( Sun 11:00 AM - 2hr - Reg Desk ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Ayem_Willing ) show
6 132 moveto
( Furries!!!  (moderating) ) show
9 124 moveto
( Sat 7:00 PM - 1hr - Pres. Suite ) show
6 116 moveto
( Age Play Panel ) show
9 108 moveto
( Sat 9:45 PM - 1hr 30min - 518 ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Bendyogagirl  ) show
6 132 moveto
( Bendyogagirl in Green Room (moderating) ) show
9 124 moveto
( Fri 12:00 PM - 10hr - Green Room ) show
6 116 moveto
( MonoPoly ) show
9 108 moveto
( Fri 6:15 PM - 1hr 30min - South County ) show
6 100 moveto
( Yoga for Kinksters (moderating) ) show
9 92 moveto
( Sat 10:45 AM - 1hr 30min - Naragansett A ) show
6 84 moveto
( So You Like it Rough? (moderating) ) show
9 76 moveto
( Sat 1:00 PM - 1hr 30min - Naragansett A ) show
6 68 moveto
( Whose Kink is it Anyway? ) show
9 60 moveto
( Sat 4:30 PM - 1hr 30min - Naragansett A ) show
6 52 moveto
( Compassionate Communication (moderating) ) show
9 44 moveto
( Sat 6:15 PM - 1hr 30min - Blackstone ) show
6 36 moveto
( Spirituality in BDSM ) show
9 28 moveto
( Sat 11:30 PM - 1hr 30min - South County ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( BIG EYES ) show
6 132 moveto
( ID Checker - 8pm Fri ) show
9 124 moveto
( Fri 8:00 PM - 2hr - Reg Desk-ID ) show
6 116 moveto
( NELA Associates Table - 9am Sat ) show
9 108 moveto
( Sat 9:00 AM - 1hr - NELA Associates Sales ) show
6 100 moveto
( NELA Associates Table - 10am Sat ) show
9 92 moveto
( Sat 10:00 AM - 2hr - NELA Associates Sales ) show
6 84 moveto
( ID Checker - 12pm Sat ) show
9 76 moveto
( Sat 12:00 PM - 2hr - Reg Desk-ID ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Bil ) show
6 132 moveto
( ID Checker - 12pm Sat ) show
9 124 moveto
( Sat 12:00 PM - 2hr - Reg Desk-ID ) show
6 116 moveto
( 5th Floor - 10am Sun ) show
9 108 moveto
( Sun 10:00 AM - 2hr - CM-5th Floor ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Bluette ) show
6 132 moveto
( Volunteer Lounge 11am Sat ) show
9 124 moveto
( Sat 11:00 AM - 2hr - Boardroom ) show
6 116 moveto
( ID Checker - 2pm Sat ) show
9 108 moveto
( Sat 2:00 PM - 2hr - Reg Desk-ID ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Blur ) show
6 132 moveto
( Disability Assistance - 10am Sun ) show
9 124 moveto
( Sun 10:00 AM - 2hr - ADA ) show
6 116 moveto
( Doors and Floors - 12pm Sun ) show
9 108 moveto
( Sun 12:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Bob ) show
6 132 moveto
( Doors and Floors - 5pm Sat ) show
9 124 moveto
( Sat 5:00 PM - 2hr - Doors-Floors ) show
6 116 moveto
( ID Checker - 9am Sun ) show
9 108 moveto
( Sun 9:00 AM - 2hr - Reg Desk-ID ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Brandon ) show
6 132 moveto
( Load In: Team 6 - 2pm Fri ) show
9 124 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 6 ) show
6 116 moveto
( Load In: Team 6 - 4pm Fri ) show
9 108 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 6 ) show
6 100 moveto
( NELA Sales Table - 12pm Sat ) show
9 92 moveto
( Sat 12:00 PM - 2hr - NELA Sales ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( breeanne ) show
6 132 moveto
( Load In: Team 3 - 10am Fri ) show
9 124 moveto
( Fri 10:00 AM - 2hr - Loading Dock - Team 3 ) show
6 116 moveto
( Load In: Team 3 - 12pm Fri ) show
9 108 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 3 ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Buddy J ) show
6 132 moveto
( NELA Sales Table - 9am Sat ) show
9 124 moveto
( Sat 9:00 AM - 1hr - NELA Sales ) show
6 116 moveto
( Wristbander - 10am Sat ) show
9 108 moveto
( Sat 10:00 AM - 2hr - Reg Desk ) show
6 100 moveto
( Wristbander - 12pm Sat ) show
9 92 moveto
( Sat 12:00 PM - 2hr - Reg Desk ) show
6 84 moveto
( Wristbander - 9am Sun ) show
9 76 moveto
( Sun 9:00 AM - 2hr - Reg Desk ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Calexir ) show
6 132 moveto
( Disability Assistance - 11am Sat ) show
9 124 moveto
( Sat 11:00 AM - 2hr - ADA ) show
6 116 moveto
( Disability Assistance - 3pm Sat ) show
9 108 moveto
( Sat 3:00 PM - 2hr - ADA ) show
6 100 moveto
( Disability Assistance - 12pm Sun ) show
9 92 moveto
( Sun 12:00 PM - 2hr - ADA ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Cameryn Moore ) show
6 132 moveto
( Power Play Load In (moderating) ) show
9 124 moveto
( Fri 5:00 PM - 45min - Blackstone ) show
6 116 moveto
( Cameryn Moore:  Power I Play (moderating) ) show
9 108 moveto
( Fri 5:45 PM - 1hr 45min - Blackstone ) show
6 100 moveto
( Power Play Load Out (moderating) ) show
9 92 moveto
( Fri 7:30 PM - 30min - Blackstone ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Carnivorous ) show
6 132 moveto
( Bi/Poly/Switch Panel ) show
9 124 moveto
( Sat 11:30 PM - 1hr 30min - Rest. Annex ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Carolyn ) show
6 132 moveto
( Load In: Team 3 - 6pm Fri ) show
9 124 moveto
( Fri 6:00 PM - 2hr - Loading Dock - Team 3 ) show
6 116 moveto
( ID Checker - 9am Sat ) show
9 108 moveto
( Sat 9:00 AM - 1hr - Reg Desk-ID ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Casey ) show
6 132 moveto
( Load In: Team 1 - 12pm Fri ) show
9 124 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 1 ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Chunsa ) show
6 132 moveto
( Doors and Floors - 10am Sun ) show
9 124 moveto
( Sun 10:00 AM - 2hr - Doors-Floors ) show
6 116 moveto
( Doors and Floors - 12pm Sun ) show
9 108 moveto
( Sun 12:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Cirra ) show
6 132 moveto
( Wristbander - 8pm Fri ) show
9 124 moveto
( Fri 8:00 PM - 2hr - Reg Desk ) show
6 116 moveto
( Wristbander - 10am Sat ) show
9 108 moveto
( Sat 10:00 AM - 2hr - Reg Desk ) show
6 100 moveto
( Wristbander - 1pm Sun ) show
9 92 moveto
( Sun 1:00 PM - 2hr - Reg Desk ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( coedwrestler35 ) show
6 132 moveto
( Compassionate Communication (outside wristband checker) ) show
9 124 moveto
( Sat 6:15 PM - 1hr 30min - Blackstone ) show
6 116 moveto
( Poly 101 (announcer/inside room attendant) ) show
9 108 moveto
( Sat 9:45 PM - 1hr 30min - Rest. Annex ) show
6 100 moveto
( Spirituality in BDSM (outside wristband checker) ) show
9 92 moveto
( Sat 11:30 PM - 1hr 30min - South County ) show
6 84 moveto
( Arm Bindings (outside wristband checker) ) show
9 76 moveto
( Sun 10:00 AM - 1hr 30min - Naragansett C ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Coral ) show
6 132 moveto
( Uniform fetish from the Mundane to the Military  (moderating) ) show
9 124 moveto
( Sat 9:00 AM - 1hr 30min - Naragansett C ) show
6 116 moveto
( Coral and the Chocolate Fetish (moderating) ) show
9 108 moveto
( Sat 1:00 PM - 1hr 30min - 418 ) show
6 100 moveto
( Spirituality in BDSM ) show
9 92 moveto
( Sat 11:30 PM - 1hr 30min - South County ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Cory ) show
6 132 moveto
( Load In: Team 1 - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 1 ) show
6 116 moveto
( Load Out: Team 1 - 4pm Sun ) show
9 108 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 1 ) show
6 100 moveto
( Load Out: Team 1 - 6pm Sun ) show
9 92 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 1 ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Courtney Jane ) show
6 132 moveto
( Courtney Jane in Green Room (moderating) ) show
9 124 moveto
( Sat 9:00 AM - 3hr 30min - Green Room ) show
6 116 moveto
( Fuck Dogma (announcer/inside room attendant) ) show
9 108 moveto
( Sat 1:00 PM - 1hr 30min - Naragansett C ) show
6 100 moveto
( Romantic Surrender (announcer/inside room attendant) ) show
9 92 moveto
( Sat 2:45 PM - 1hr 30min - Naragansett C ) show
6 84 moveto
( Whose Kink is it Anyway? (announcer/inside room attendant) ) show
9 76 moveto
( Sat 4:30 PM - 1hr 30min - Naragansett A ) show
6 68 moveto
( Power and Authority Exchange Relationships (moderating) ) show
9 60 moveto
( Sat 11:30 PM - 1hr 30min - 518 ) show
6 52 moveto
( Getting Good Head (moderating) ) show
9 44 moveto
( Sun 11:45 AM - 1hr 30min - 518 ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Cwellan ) show
6 132 moveto
( Load In: Team 3 - 2pm Fri ) show
9 124 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 3 ) show
6 116 moveto
( Load In: Team 3 - 4pm Fri ) show
9 108 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 3 ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Dakota ) show
6 132 moveto
( Volunteer Lounge 5pm Sat ) show
9 124 moveto
( Sat 5:00 PM - 2hr - Boardroom ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Dan ) show
6 132 moveto
( How to Throw a Whip (announcer/inside room attendant) ) show
9 124 moveto
( Fri 8:00 PM - 1hr 30min - South County ) show
6 116 moveto
( VASE Meetup ) show
9 108 moveto
( Sat 4:00 PM - 2hr - Pres. Suite ) show
6 100 moveto
( NOT your Grandma's Enema (announcer/inside room attendant) ) show
9 92 moveto
( Sat 8:00 PM - 1hr 30min - 418 ) show
6 84 moveto
( Aural Sex : Seduction by Voice &  Story Telling  (announcer/inside room attendant) ) show
9 76 moveto
( Sun 1:45 PM - 1hr 30min - Naragansett C ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Dana ) show
6 132 moveto
( Volunteer Lounge 3pm Sat ) show
9 124 moveto
( Sat 3:00 PM - 2hr - Boardroom ) show
6 116 moveto
( Doors and Floors - 5pm Sat ) show
9 108 moveto
( Sat 5:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Danielle dv8 ) show
6 132 moveto
( Vaginal and Anal Fisting 101 (moderating) ) show
9 124 moveto
( Sat 4:30 PM - 1hr 30min - 518 ) show
6 116 moveto
( Erogenous Zoning Violations (moderating) ) show
9 108 moveto
( Sat 11:30 PM - 1hr 30min - Blackstone ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Darksideblues  ) show
6 132 moveto
( Load In: Team 5 - 2pm Fri ) show
9 124 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 5 ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( David Wraith ) show
6 132 moveto
( MonoPoly ) show
9 124 moveto
( Fri 6:15 PM - 1hr 30min - South County ) show
6 116 moveto
( Sex Positive: What does it mean? (moderating) ) show
9 108 moveto
( Sat 1:00 PM - 1hr 30min - Rest. Annex ) show
6 100 moveto
( Poly 101 (moderating) ) show
9 92 moveto
( Sat 9:45 PM - 1hr 30min - Rest. Annex ) show
6 84 moveto
( Alternative Activism ) show
9 76 moveto
( Sun 1:45 PM - 1hr 30min - 518 ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( De Lano ) show
6 132 moveto
( Scene Survival for New Players (moderating) ) show
9 124 moveto
( Fri 8:00 PM - 1hr 30min - Rest. Annex ) show
6 116 moveto
( Rope Bottom: An Advanced Guide for Frequent Flyer (moderating) ) show
9 108 moveto
( Sat 10:45 AM - 1hr 30min - 418 ) show
6 100 moveto
( Connection and Intent ) show
9 92 moveto
( Sat 9:45 PM - 1hr 30min - South County ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Dean ) show
6 132 moveto
( Load In: Team 2 - 6pm Fri ) show
9 124 moveto
( Fri 6:00 PM - 2hr - Loading Dock - Team 2 ) show
6 116 moveto
( Doors and Floors - 12pm Sun ) show
9 108 moveto
( Sun 12:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Deanna ) show
6 132 moveto
( 4th Floor - 9am Sat ) show
9 124 moveto
( Sat 9:00 AM - 2hr - CM-4th Floor ) show
6 116 moveto
( ID Checker - 9am Sun ) show
9 108 moveto
( Sun 9:00 AM - 2hr - Reg Desk-ID ) show
6 100 moveto
( Volunteer Lounge 5pm Sun ) show
9 92 moveto
( Sun 5:00 PM - 2hr - Boardroom ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Deb ) show
6 132 moveto
( Volunteer Lounge 10am Fri ) show
9 124 moveto
( Fri 10:00 AM - 2hr - Boardroom ) show
6 116 moveto
( Volunteer Lounge noon Fri ) show
9 108 moveto
( Fri 12:00 PM - 2hr - Boardroom ) show
6 100 moveto
( Volunteer Lounge 2pm Fri ) show
9 92 moveto
( Fri 2:00 PM - 2hr - Boardroom ) show
6 84 moveto
( Volunteer Lounge 4pm Fri ) show
9 76 moveto
( Fri 4:00 PM - 2hr - Boardroom ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Debbie ) show
6 132 moveto
( Load Out: Team 3 - 4pm Sun ) show
9 124 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 3 ) show
6 116 moveto
( Load Out: Team 3 - 6pm Sun ) show
9 108 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 3 ) show
6 100 moveto
( Load Out: Team 3 - 8pm Sun ) show
9 92 moveto
( Sun 8:00 PM - 2hr - Loading Dock - Team 3 ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Dee ) show
6 132 moveto
( Traveling the Yellow Brick Road of Kink (announcer/inside room attendant) ) show
9 124 moveto
( Sat 10:45 AM - 1hr 30min - 518 ) show
6 116 moveto
( Mommy Dearest (announcer/inside room attendant) ) show
9 108 moveto
( Sat 1:00 PM - 1hr 30min - 518 ) show
6 100 moveto
( Catheters and Sounds (announcer/inside room attendant) ) show
9 92 moveto
( Sun 10:00 AM - 1hr 30min - 518 ) show
6 84 moveto
( Tickle Play with P.E.T.E (announcer/inside room attendant) ) show
9 76 moveto
( Sun 1:45 PM - 1hr 30min - Naragansett B ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( demonic angel ) show
6 132 moveto
( Load In: Team 1 - 12pm Fri ) show
9 124 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 1 ) show
6 116 moveto
( Load In: Team 1 - 2pm Fri ) show
9 108 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 1 ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Deyan ) show
6 132 moveto
( Wristbander - 11am Sun ) show
9 124 moveto
( Sun 11:00 AM - 2hr - Reg Desk ) show
6 116 moveto
( Wristbander - 1pm Sun ) show
9 108 moveto
( Sun 1:00 PM - 2hr - Reg Desk ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Dm Eric S ) show
6 132 moveto
( Load In: Team 3 - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 3 ) show
6 116 moveto
( Load In: Team 3 - 6pm Fri ) show
9 108 moveto
( Fri 6:00 PM - 2hr - Loading Dock - Team 3 ) show
6 100 moveto
( Volunteer Lounge 9am Sat ) show
9 92 moveto
( Sat 9:00 AM - 2hr - Boardroom ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Doc ) show
6 132 moveto
( Load Out: Team 2 - 4pm Sun ) show
9 124 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 2 ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Domino ) show
6 132 moveto
( Whip Lounge (moderating) ) show
9 124 moveto
( Sat 10:30 AM - 7hr 30min - South County ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Don ) show
6 132 moveto
( Load In: Team 2 - 10am Fri ) show
9 124 moveto
( Fri 10:00 AM - 2hr - Loading Dock - Team 2 ) show
6 116 moveto
( Volunteer Lounge 9am Sat ) show
9 108 moveto
( Sat 9:00 AM - 2hr - Boardroom ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Donna ) show
6 132 moveto
( Load In: Team 3 - 12pm Fri ) show
9 124 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 3 ) show
6 116 moveto
( Load In: Team 3 - 2pm Fri ) show
9 108 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 3 ) show
6 100 moveto
( Load Out: Team 3 - 4pm Sun ) show
9 92 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 3 ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Dorje ) show
6 132 moveto
( NELA Sales Table - 10am Sat ) show
9 124 moveto
( Sat 10:00 AM - 2hr - NELA Sales ) show
6 116 moveto
( Wristbander - 1pm Sun ) show
9 108 moveto
( Sun 1:00 PM - 2hr - Reg Desk ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Dov ) show
6 132 moveto
( How to Throw a Whip (moderating) ) show
9 124 moveto
( Fri 8:00 PM - 1hr 30min - South County ) show
6 116 moveto
( So You Like it Rough? ) show
9 108 moveto
( Sat 1:00 PM - 1hr 30min - Naragansett A ) show
6 100 moveto
( Guerrilla Fetish Photography and the Law (moderating) ) show
9 92 moveto
( Sun 1:45 PM - 1hr 30min - Rest. Annex ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Dr. Robert J. Rubel ) show
6 132 moveto
( Playing with Saran Wrap (moderating) ) show
9 124 moveto
( Sat 4:30 PM - 1hr 30min - Rest. Annex ) show
6 116 moveto
( Playing with the Pussy (moderating) ) show
9 108 moveto
( Sun 10:00 AM - 1hr 30min - Naragansett B ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Dr. SlashBlight ) show
6 132 moveto
( New Designers Fetish Fashionshow (announcer/inside room attendant) ) show
9 124 moveto
( Fri 9:00 PM - 2hr 30min - Naragansett A ) show
6 116 moveto
( Whose Kink is it Anyway? (moderating) ) show
9 108 moveto
( Sat 4:30 PM - 1hr 30min - Naragansett A ) show
6 100 moveto
( Erotic Hypnosis 101 with DrSlashBlight (moderating) ) show
9 92 moveto
( Sat 8:00 PM - 1hr 30min - Blackstone ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Dragon ) show
6 132 moveto
( Load In: Team 5 - 12pm Fri ) show
9 124 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 5 ) show
6 116 moveto
( Volunteer Lounge 2pm Fri ) show
9 108 moveto
( Fri 2:00 PM - 2hr - Boardroom ) show
6 100 moveto
( ID Checker - 12pm Sat ) show
9 92 moveto
( Sat 12:00 PM - 2hr - Reg Desk-ID ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Dragonfly ) show
6 132 moveto
( Load In: Team 2 - 12pm Fri ) show
9 124 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 2 ) show
6 116 moveto
( Load In: Team 2 - 2pm Fri ) show
9 108 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 2 ) show
6 100 moveto
( Load In: Team 2 - 4pm Fri ) show
9 92 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 2 ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( DragonScott ) show
6 132 moveto
( Load In: Team 4 - 2pm Fri ) show
9 124 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 4 ) show
6 116 moveto
( Load In: Team 4 - 4pm Fri ) show
9 108 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 4 ) show
6 100 moveto
( Load In: Team 4 - 6pm Fri ) show
9 92 moveto
( Fri 6:00 PM - 2hr - Loading Dock - Team 4 ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Drew ) show
6 132 moveto
( Load In: Team 5 - 12pm Fri ) show
9 124 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 5 ) show
6 116 moveto
( Load In: Team 5 - 2pm Fri ) show
9 108 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 5 ) show
6 100 moveto
( Load In: Team 5 - 4pm Fri ) show
9 92 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 5 ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( DrIrv ) show
6 132 moveto
( Catheters and Sounds (moderating) ) show
9 124 moveto
( Sun 10:00 AM - 1hr 30min - 518 ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Dsire ) show
6 132 moveto
( ID Checker - 12pm Sat ) show
9 124 moveto
( Sat 12:00 PM - 2hr - Reg Desk-ID ) show
6 116 moveto
( ID Checker - 1pm Sun ) show
9 108 moveto
( Sun 1:00 PM - 2hr - Reg Desk-ID ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Duncan ) show
6 132 moveto
( Load In: Team 1 - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 1 ) show
6 116 moveto
( Load In: Team 1 - 6pm Fri ) show
9 108 moveto
( Fri 6:00 PM - 2hr - Loading Dock - Team 1 ) show
6 100 moveto
( Load In: Team 1 - 8pm Fri ) show
9 92 moveto
( Fri 8:00 PM - 2hr - Loading Dock - Team 1 ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Dunter  ) show
6 132 moveto
( Pressure Points (moderating) ) show
9 124 moveto
( Sat 9:45 PM - 1hr 30min - 418 ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Dyanne ) show
6 132 moveto
( Load In: Team 5 - 2pm Fri ) show
9 124 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 5 ) show
6 116 moveto
( Load In: Team 5 - 4pm Fri ) show
9 108 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 5 ) show
6 100 moveto
( Load Out: Team 1 - 4pm Sun ) show
9 92 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 1 ) show
6 84 moveto
( Load Out: Team 1 - 6pm Sun ) show
9 76 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 1 ) show
6 68 moveto
( Load Out: Team 1 - 8pm Sun ) show
9 60 moveto
( Sun 8:00 PM - 2hr - Loading Dock - Team 1 ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Ed Drohan ) show
6 132 moveto
( Load In: Team 4 - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 4 ) show
6 116 moveto
( ID Checker - 6pm Fri ) show
9 108 moveto
( Fri 6:00 PM - 2hr - Reg Desk-ID ) show
6 100 moveto
( Disability Assistance - 11am Sat (moderating) ) show
9 92 moveto
( Sat 11:00 AM - 2hr - ADA ) show
6 84 moveto
( Age Play Panel (announcer/inside room attendant) ) show
9 76 moveto
( Sat 9:45 PM - 1hr 30min - 518 ) show
6 68 moveto
( Bi/Poly/Switch Panel (outside wristband checker) ) show
9 60 moveto
( Sat 11:30 PM - 1hr 30min - Rest. Annex ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Edd ) show
6 132 moveto
( Load In: Team 3 - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 3 ) show
6 116 moveto
( Load In: Team 3 - 6pm Fri ) show
9 108 moveto
( Fri 6:00 PM - 2hr - Loading Dock - Team 3 ) show
6 100 moveto
( Load In: Team 3 - 8pm Fri ) show
9 92 moveto
( Fri 8:00 PM - 2hr - Loading Dock - Team 3 ) show
6 84 moveto
( Load Out: Team 1 - 6pm Sun ) show
9 76 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 1 ) show
6 68 moveto
( Load Out: Team 1 - 8pm Sun ) show
9 60 moveto
( Sun 8:00 PM - 2hr - Loading Dock - Team 1 ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Elisabetta ) show
6 132 moveto
( Go Team Slutty! (moderating) ) show
9 124 moveto
( Sat 1:00 PM - 1hr 30min - Naragansett B ) show
6 116 moveto
( Discover your Queer Identity ) show
9 108 moveto
( Sun 10:00 AM - 1hr 30min - 418 ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( elizabeth ) show
6 132 moveto
( Wristbander - 10am Sat ) show
9 124 moveto
( Sat 10:00 AM - 2hr - Reg Desk ) show
6 116 moveto
( Wristbander - 12pm Sat ) show
9 108 moveto
( Sat 12:00 PM - 2hr - Reg Desk ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Elizabeth DuPre ) show
6 132 moveto
( Power Play Load In (assisting) ) show
9 124 moveto
( Fri 5:00 PM - 45min - Blackstone ) show
6 116 moveto
( Cameryn Moore:  Power I Play (assisting) ) show
9 108 moveto
( Fri 5:45 PM - 1hr 45min - Blackstone ) show
6 100 moveto
( Power Play Load Out (assisting) ) show
9 92 moveto
( Fri 7:30 PM - 30min - Blackstone ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Ella ) show
6 132 moveto
( Load In: Team 6 - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 6 ) show
6 116 moveto
( Doors and Floors - 2pm Sun ) show
9 108 moveto
( Sun 2:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( ElleTrouble ) show
6 132 moveto
( New Designers Fetish Fashionshow (assisting) ) show
9 124 moveto
( Fri 9:00 PM - 2hr 30min - Naragansett B ) show
6 116 moveto
( Whose Kink is it Anyway? (assisting) ) show
9 108 moveto
( Sat 4:30 PM - 1hr 30min - Naragansett A ) show
6 100 moveto
( Erotic Hypnosis 101 with DrSlashBlight (assisting) ) show
9 92 moveto
( Sat 8:00 PM - 1hr 30min - Blackstone ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Eloff ) show
6 132 moveto
( Load Out: Team 2 - 8pm Sun ) show
9 124 moveto
( Sun 8:00 PM - 2hr - Loading Dock - Team 2 ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Emily ) show
6 132 moveto
( Doors and Floors - 12pm Sun ) show
9 124 moveto
( Sun 12:00 PM - 2hr - Doors-Floors ) show
6 116 moveto
( Doors and Floors - 2pm Sun ) show
9 108 moveto
( Sun 2:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Eponine ) show
6 132 moveto
( TNG Meet & Greet (moderating) ) show
9 124 moveto
( Sat 6:00 PM - 1hr - Pres. Suite ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Erika ) show
6 132 moveto
( Wristbander - 12pm Sat ) show
9 124 moveto
( Sat 12:00 PM - 2hr - Reg Desk ) show
6 116 moveto
( ID Checker - 2pm Sat ) show
9 108 moveto
( Sat 2:00 PM - 2hr - Reg Desk-ID ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Evey ) show
6 132 moveto
( Wristbander - 2pm Sat ) show
9 124 moveto
( Sat 2:00 PM - 2hr - Reg Desk ) show
6 116 moveto
( ID Checker - 11am Sun ) show
9 108 moveto
( Sun 11:00 AM - 2hr - Reg Desk-ID ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Fallen's Own ) show
6 132 moveto
( Load Out: Team 3 - 4pm Sun ) show
9 124 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 3 ) show
6 116 moveto
( Load Out: Team 3 - 6pm Sun ) show
9 108 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 3 ) show
6 100 moveto
( Load Out: Team 3 - 8pm Sun ) show
9 92 moveto
( Sun 8:00 PM - 2hr - Loading Dock - Team 3 ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Fawnn ) show
6 132 moveto
( Load In: Team 1 - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 1 ) show
6 116 moveto
( Load In: Team 1 - 6pm Fri ) show
9 108 moveto
( Fri 6:00 PM - 2hr - Loading Dock - Team 1 ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Ferrous Caput ) show
6 132 moveto
( Load In: Team 1 - 12pm Fri ) show
9 124 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 1 ) show
6 116 moveto
( Load In: Team 1 - 2pm Fri ) show
9 108 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 1 ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Flirtrageous ) show
6 132 moveto
( ID Checker - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Reg Desk-ID ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Frank ) show
6 132 moveto
( Load In: Team 3 - 6pm Fri ) show
9 124 moveto
( Fri 6:00 PM - 2hr - Loading Dock - Team 3 ) show
6 116 moveto
( ID Checker - 9am Sat ) show
9 108 moveto
( Sat 9:00 AM - 1hr - Reg Desk-ID ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( FuzzyJim ) show
6 132 moveto
( Puppy Play with Rabbit (announcer/inside room attendant) ) show
9 124 moveto
( Fri 6:15 PM - 1hr 30min - 518 ) show
6 116 moveto
( Tying Men (announcer/inside room attendant) ) show
9 108 moveto
( Fri 9:45 PM - 1hr 30min - Rest. Annex ) show
6 100 moveto
( Healthy, Wealthy, and Wise (outside wristband checker) ) show
9 92 moveto
( Sat 9:00 AM - 1hr 30min - 418 ) show
6 84 moveto
( Dark Role Play (announcer/inside room attendant) ) show
9 76 moveto
( Sat 10:45 AM - 1hr 30min - Naragansett C ) show
6 68 moveto
( NOT your Grandma's Enema (outside wristband checker) ) show
9 60 moveto
( Sat 8:00 PM - 1hr 30min - 418 ) show
6 52 moveto
( Pressure Points (announcer/inside room attendant) ) show
9 44 moveto
( Sat 9:45 PM - 1hr 30min - 418 ) show
6 36 moveto
( How to Give/Go to a Play Party ... (announcer/inside room attendant) ) show
9 28 moveto
( Sat 11:30 PM - 1hr 30min - 418 ) show
6 20 moveto
( Playing with the Pussy (outside wristband checker) ) show
9 12 moveto
( Sun 10:00 AM - 1hr 30min - Naragansett B ) show
6 4 moveto
( ApocalyptaKink Workshop: When Sh*t Happens (outside wristband checker) ) show
9 -4 moveto
( Sun 11:45 AM - 1hr 30min - Naragansett B ) show
6 -12 moveto
( The Lazy Top's Guide to Pacing a Scene  (outside wristband checker) ) show
9 -20 moveto
( Sun 1:45 PM - 1hr 30min - Naragansett A ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( FyreWalkyr ) show
6 132 moveto
( Doors and Floors - 11am Sat ) show
9 124 moveto
( Sat 11:00 AM - 2hr - Doors-Floors ) show
6 116 moveto
( Doors and Floors - 3pm Sat ) show
9 108 moveto
( Sat 3:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( girlMouse ) show
6 132 moveto
( Tie 'Em up and Fuck 'Em (announcer/inside room attendant) ) show
9 124 moveto
( Sat 9:00 AM - 1hr 30min - Naragansett B ) show
6 116 moveto
( Arm Bindings (announcer/inside room attendant) ) show
9 108 moveto
( Sun 10:00 AM - 1hr 30min - Naragansett C ) show
6 100 moveto
( ApocalyptaKink Workshop: When Sh*t Happens (announcer/inside room attendant) ) show
9 92 moveto
( Sun 11:45 AM - 1hr 30min - Naragansett B ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Goddess Diane ) show
6 132 moveto
( Disability Assistance - 8pm Fri ) show
9 124 moveto
( Fri 8:00 PM - 2hr - ADA ) show
6 116 moveto
( Disability Assistance - 5pm Sat ) show
9 108 moveto
( Sat 5:00 PM - 2hr - ADA ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Grace ) show
6 132 moveto
( Load In: Team 5 - 6pm Fri ) show
9 124 moveto
( Fri 6:00 PM - 2hr - Loading Dock - Team 5 ) show
6 116 moveto
( Narr C - 11am Sat ) show
9 108 moveto
( Sat 11:00 AM - 2hr - CM-Narr C ) show
6 100 moveto
( Doors and Floors - 2pm Sun ) show
9 92 moveto
( Sun 2:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Graydancer ) show
6 132 moveto
( TwoPlay: The Art of Making Out (moderating) ) show
9 124 moveto
( Fri 8:00 PM - 1hr 30min - Blackstone ) show
6 116 moveto
( Tie 'Em up and Fuck 'Em (moderating) ) show
9 108 moveto
( Sat 9:00 AM - 1hr 30min - Naragansett B ) show
6 100 moveto
( Whose Kink is it Anyway? ) show
9 92 moveto
( Sat 4:30 PM - 1hr 30min - Naragansett A ) show
6 84 moveto
( Bi/Poly/Switch Panel ) show
9 76 moveto
( Sat 11:30 PM - 1hr 30min - Rest. Annex ) show
6 68 moveto
( ApocalyptaKink Workshop: When Sh*t Happens (moderating) ) show
9 60 moveto
( Sun 11:45 AM - 1hr 30min - Naragansett B ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Heather ) show
6 132 moveto
( Volunteer Coordinator - 11am Fri (moderating) ) show
9 124 moveto
( Fri 11:00 AM - 1hr - Volunteer Coordinator ) show
6 116 moveto
( Volunteer Coordinator - noon Fri (moderating) ) show
9 108 moveto
( Fri 12:00 PM - 1hr - Volunteer Coordinator ) show
6 100 moveto
( Volunteer Coordinator - 1pm Fri (moderating) ) show
9 92 moveto
( Fri 1:00 PM - 1hr - Volunteer Coordinator ) show
6 84 moveto
( Volunteer Coordinator - 2pm Fri (moderating) ) show
9 76 moveto
( Fri 2:00 PM - 1hr - Volunteer Coordinator ) show
6 68 moveto
( Volunteer Coordinator - 3pm Fri (moderating) ) show
9 60 moveto
( Fri 3:00 PM - 1hr - Volunteer Coordinator ) show
6 52 moveto
( Volunteer Coordinator - 4pm Fri (moderating) ) show
9 44 moveto
( Fri 4:00 PM - 1hr - Volunteer Coordinator ) show
6 36 moveto
( Volunteer Coordinator - 5pm Fri (moderating) ) show
9 28 moveto
( Fri 5:00 PM - 1hr - Volunteer Coordinator ) show
6 20 moveto
( Volunteer Coordinator - 6pm Fri ) show
9 12 moveto
( Fri 6:00 PM - 1hr - Volunteer Coordinator ) show
6 4 moveto
( Volunteer Coordinator - 7pm Fri ) show
9 -4 moveto
( Fri 7:00 PM - 1hr - Volunteer Coordinator ) show
6 -12 moveto
( Volunteer Coordinator - 8pm Fri (moderating) ) show
9 -20 moveto
( Fri 8:00 PM - 1hr - Volunteer Coordinator ) show
6 -28 moveto
( Volunteer Coordinator - 9am Sat (moderating) ) show
9 -36 moveto
( Sat 9:00 AM - 1hr - Volunteer Coordinator ) show
6 -44 moveto
( Volunteer Coordinator - 10am Sat ) show
9 -52 moveto
( Sat 10:00 AM - 1hr - Volunteer Coordinator ) show
6 -60 moveto
( Volunteer Coordinator - 11am Sat (moderating) ) show
9 -68 moveto
( Sat 11:00 AM - 1hr - Volunteer Coordinator ) show
6 -76 moveto
( Volunteer Coordinator - noon Sat (moderating) ) show
9 -84 moveto
( Sat 12:00 PM - 1hr - Volunteer Coordinator ) show
6 -92 moveto
( Volunteer Coordinator - 1pm Sat (moderating) ) show
9 -100 moveto
( Sat 1:00 PM - 1hr - Volunteer Coordinator ) show
6 -108 moveto
( Volunteer Coordinator - 2pm Sat (moderating) ) show
9 -116 moveto
( Sat 2:00 PM - 1hr - Volunteer Coordinator ) show
6 -124 moveto
( Volunteer Coordinator - 3pm Sat (moderating) ) show
9 -132 moveto
( Sat 3:00 PM - 1hr - Volunteer Coordinator ) show
6 -140 moveto
( Volunteer Coordinator - 4pm Sat (moderating) ) show
9 -148 moveto
( Sat 4:00 PM - 1hr - Volunteer Coordinator ) show
6 -156 moveto
( Volunteer Coordinator - 5pm Sat (moderating) ) show
9 -164 moveto
( Sat 5:00 PM - 1hr - Volunteer Coordinator ) show
6 -172 moveto
( Volunteer Coordinator - 6pm Sat (moderating) ) show
9 -180 moveto
( Sat 6:00 PM - 1hr - Volunteer Coordinator ) show
6 -188 moveto
( Volunteer Coordinator - 9am Sun (moderating) ) show
9 -196 moveto
( Sun 9:00 AM - 1hr - Volunteer Coordinator ) show
6 -204 moveto
( Volunteer Coordinator - 10am Sun (moderating) ) show
9 -212 moveto
( Sun 10:00 AM - 1hr - Volunteer Coordinator ) show
6 -220 moveto
( Volunteer Coordinator - 11am Sun (moderating) ) show
9 -228 moveto
( Sun 11:00 AM - 1hr - Volunteer Coordinator ) show
6 -236 moveto
( Volunteer Coordinator - noon Sun (moderating) ) show
9 -244 moveto
( Sun 12:00 PM - 1hr - Volunteer Coordinator ) show
6 -252 moveto
( Volunteer Coordinator - 1pm Sun (moderating) ) show
9 -260 moveto
( Sun 1:00 PM - 1hr - Volunteer Coordinator ) show
6 -268 moveto
( Volunteer Coordinator - 2pm Sun (moderating) ) show
9 -276 moveto
( Sun 2:00 PM - 1hr - Volunteer Coordinator ) show
6 -284 moveto
( Volunteer Coordinator - 3pm Sun (moderating) ) show
9 -292 moveto
( Sun 3:00 PM - 1hr - Volunteer Coordinator ) show
6 -300 moveto
( Volunteer Coordinator - 4pm Sun (moderating) ) show
9 -308 moveto
( Sun 4:00 PM - 1hr - Volunteer Coordinator ) show
6 -316 moveto
( Volunteer Coordinator - 5pm Sun (moderating) ) show
9 -324 moveto
( Sun 5:00 PM - 1hr - Volunteer Coordinator ) show
6 -332 moveto
( Volunteer Coordinator - 6pm Sun (moderating) ) show
9 -340 moveto
( Sun 6:00 PM - 1hr - Volunteer Coordinator ) show
6 -348 moveto
( Volunteer Coordinator - 7pm Sun (moderating) ) show
9 -356 moveto
( Sun 7:00 PM - 1hr - Volunteer Coordinator ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Herbie ) show
6 132 moveto
( Getting Good Head (assisting) ) show
9 124 moveto
( Sun 11:45 AM - 1hr 30min - 518 ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Hermes ) show
6 132 moveto
( Load In: Team 1 - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 1 ) show
6 116 moveto
( Load In: Team 1 - 6pm Fri ) show
9 108 moveto
( Fri 6:00 PM - 2hr - Loading Dock - Team 1 ) show
6 100 moveto
( Load In: Team 1 - 8pm Fri ) show
9 92 moveto
( Fri 8:00 PM - 2hr - Loading Dock - Team 1 ) show
6 84 moveto
( Doors and Floors - 9am Sat ) show
9 76 moveto
( Sat 9:00 AM - 2hr - Doors-Floors ) show
6 68 moveto
( Setting the Scene and Building Trust (announcer/inside room attendant) ) show
9 60 moveto
( Sun 11:45 AM - 1hr 30min - Naragansett A ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Hobbes_Kitten ) show
6 132 moveto
( Load Out: Team 2 - 4pm Sun ) show
9 124 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 2 ) show
6 116 moveto
( Load Out: Team 2 - 6pm Sun ) show
9 108 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 2 ) show
6 100 moveto
( Load Out: Team 2 - 8pm Sun ) show
9 92 moveto
( Sun 8:00 PM - 2hr - Loading Dock - Team 2 ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( hockeysub ) show
6 132 moveto
( Load Out: Team 6 - 6pm Sun ) show
9 124 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 6 ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Honore ) show
6 132 moveto
( ID Checker - 4pm Sat ) show
9 124 moveto
( Sat 4:00 PM - 2hr - Reg Desk-ID ) show
6 116 moveto
( ID Checker - 1pm Sun ) show
9 108 moveto
( Sun 1:00 PM - 2hr - Reg Desk-ID ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( imasupermuteant ) show
6 132 moveto
( Applied Rigging for Brain and Hands (announcer/inside room attendant) ) show
9 124 moveto
( Fri 9:45 PM - 1hr 30min - South County ) show
6 116 moveto
( Introduction to Rope Bondage (announcer/inside room attendant) ) show
9 108 moveto
( Sat 10:45 AM - 1hr 30min - Rest. Annex ) show
6 100 moveto
( Monogamy Without Shame (announcer/inside room attendant) ) show
9 92 moveto
( Sun 11:45 AM - 1hr 30min - Rest. Annex ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( J3remoo ) show
6 132 moveto
( Volunteer Lounge 11am Sun ) show
9 124 moveto
( Sun 11:00 AM - 2hr - Boardroom ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Jack Frost ) show
6 132 moveto
( Doors and Floors - 9am Sat ) show
9 124 moveto
( Sat 9:00 AM - 2hr - Doors-Floors ) show
6 116 moveto
( Doors and Floors - 11am Sat ) show
9 108 moveto
( Sat 11:00 AM - 2hr - Doors-Floors ) show
6 100 moveto
( Doors and Floors - 1pm Sat ) show
9 92 moveto
( Sat 1:00 PM - 2hr - Doors-Floors ) show
6 84 moveto
( Doors and Floors - 10am Sun ) show
9 76 moveto
( Sun 10:00 AM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Jack Steel ) show
6 132 moveto
( Load In: Team 6 - 10am Fri ) show
9 124 moveto
( Fri 10:00 AM - 2hr - Loading Dock - Team 6 ) show
6 116 moveto
( Load In: Team 6 - 12pm Fri ) show
9 108 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 6 ) show
6 100 moveto
( Load In: Team 6 - 2pm Fri ) show
9 92 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 6 ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Jade ) show
6 132 moveto
( ID Checker - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Reg Desk-ID ) show
6 116 moveto
( Wristbander - 9am Sat ) show
9 108 moveto
( Sat 9:00 AM - 1hr - Reg Desk ) show
6 100 moveto
( 4th Floor - 11am Sat ) show
9 92 moveto
( Sat 11:00 AM - 2hr - CM-4th Floor ) show
6 84 moveto
( ID Checker - 9am Sun ) show
9 76 moveto
( Sun 9:00 AM - 2hr - Reg Desk-ID ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Jade Kitten ) show
6 132 moveto
( Doors and Floors - 11am Sat ) show
9 124 moveto
( Sat 11:00 AM - 2hr - Doors-Floors ) show
6 116 moveto
( Doors and Floors - 3pm Sat ) show
9 108 moveto
( Sat 3:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Jakan ) show
6 132 moveto
( Load In: Team 6 - 2pm Fri ) show
9 124 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 6 ) show
6 116 moveto
( Load In: Team 6 - 4pm Fri ) show
9 108 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 6 ) show
6 100 moveto
( Load Out: Team 1 - 4pm Sun ) show
9 92 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 1 ) show
6 84 moveto
( Load Out: Team 2 - 6pm Sun ) show
9 76 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 2 ) show
6 68 moveto
( Load Out: Team 1 - 8pm Sun ) show
9 60 moveto
( Sun 8:00 PM - 2hr - Loading Dock - Team 1 ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( James ) show
6 132 moveto
( Load Out: Team 3 - 4pm Sun ) show
9 124 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 3 ) show
6 116 moveto
( Load Out: Team 3 - 6pm Sun ) show
9 108 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 3 ) show
6 100 moveto
( Load Out: Team 3 - 8pm Sun ) show
9 92 moveto
( Sun 8:00 PM - 2hr - Loading Dock - Team 3 ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Jawn's Doll ) show
6 132 moveto
( MAsT Meeting ) show
9 124 moveto
( Fri 7:00 PM - 2hr - Pres. Suite ) show
6 116 moveto
( Negotiating for Submissives (moderating) ) show
9 108 moveto
( Sat 4:30 PM - 1hr 30min - Naragansett C ) show
6 100 moveto
( Power and Authority Exchange Relationships ) show
9 92 moveto
( Sat 11:30 PM - 1hr 30min - 518 ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Jax  ) show
6 132 moveto
( Getting Good Head (assisting) ) show
9 124 moveto
( Sun 11:45 AM - 1hr 30min - 518 ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Jaye  ) show
6 132 moveto
( Load Out: Team 4 - 4pm Sun ) show
9 124 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 4 ) show
6 116 moveto
( Load Out: Team 4 - 6pm Sun ) show
9 108 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 4 ) show
6 100 moveto
( Load Out: Team 4 - 8pm Sun ) show
9 92 moveto
( Sun 8:00 PM - 2hr - Loading Dock - Team 4 ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Jean ) show
6 132 moveto
( NELA Sales Table - 4pm Sat ) show
9 124 moveto
( Sat 4:00 PM - 2hr - NELA Sales ) show
6 116 moveto
( Disability Assistance - 9pm Sat ) show
9 108 moveto
( Sat 9:00 PM - 2hr - ADA ) show
6 100 moveto
( Volunteer Lounge 11am Sun ) show
9 92 moveto
( Sun 11:00 AM - 2hr - Boardroom ) show
6 84 moveto
( ID Checker - 1pm Sun ) show
9 76 moveto
( Sun 1:00 PM - 2hr - Reg Desk-ID ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Jeannine ) show
6 132 moveto
( Load In: Team 2 - 6pm Fri ) show
9 124 moveto
( Fri 6:00 PM - 2hr - Loading Dock - Team 2 ) show
6 116 moveto
( Doors and Floors - 12pm Sun ) show
9 108 moveto
( Sun 12:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Jeff ) show
6 132 moveto
( Load Out: Team 3 - 4pm Sun ) show
9 124 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 3 ) show
6 116 moveto
( Load Out: Team 3 - 6pm Sun ) show
9 108 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 3 ) show
6 100 moveto
( Load Out: Team 3 - 8pm Sun ) show
9 92 moveto
( Sun 8:00 PM - 2hr - Loading Dock - Team 3 ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Jen ) show
6 132 moveto
( Volunteer Lounge 5pm Sat ) show
9 124 moveto
( Sat 5:00 PM - 2hr - Boardroom ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( jenphalian ) show
6 132 moveto
( Wristbander - 2pm Sat ) show
9 124 moveto
( Sat 2:00 PM - 2hr - Reg Desk ) show
6 116 moveto
( Wristbander - 4pm Sat ) show
9 108 moveto
( Sat 4:00 PM - 2hr - Reg Desk ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Jera ) show
6 132 moveto
( Doors and Floors - 3pm Sat ) show
9 124 moveto
( Sat 3:00 PM - 2hr - Doors-Floors ) show
6 116 moveto
( Doors and Floors - 5pm Sat ) show
9 108 moveto
( Sat 5:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Jesse ) show
6 132 moveto
( Wristbander - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Reg Desk ) show
6 116 moveto
( Wristbander - 6pm Fri ) show
9 108 moveto
( Fri 6:00 PM - 2hr - Reg Desk ) show
6 100 moveto
( Volunteer Lounge 8pm Fri ) show
9 92 moveto
( Fri 8:00 PM - 2hr - Boardroom ) show
6 84 moveto
( Volunteer Lounge 3pm Sat ) show
9 76 moveto
( Sat 3:00 PM - 2hr - Boardroom ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( joemash ) show
6 132 moveto
( Discovering Your Inner Sadist (outside wristband checker) ) show
9 124 moveto
( Fri 8:00 PM - 1hr 30min - 418 ) show
6 116 moveto
( Violet Wands (outside wristband checker) ) show
9 108 moveto
( Sat 9:00 AM - 1hr 30min - 518 ) show
6 100 moveto
( Dark Role Play (outside wristband checker) ) show
9 92 moveto
( Sat 10:45 AM - 1hr 30min - Naragansett C ) show
6 84 moveto
( For your own good (outside wristband checker) ) show
9 76 moveto
( Sun 11:45 AM - 1hr 30min - Naragansett C ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( John ) show
6 132 moveto
( Load In: Team 4 - 12pm Fri ) show
9 124 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 4 ) show
6 116 moveto
( Load In: Team 4 - 2pm Fri ) show
9 108 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 4 ) show
6 100 moveto
( Load In: Team 4 - 4pm Fri ) show
9 92 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 4 ) show
6 84 moveto
( Load In: Team 4 - 6pm Fri ) show
9 76 moveto
( Fri 6:00 PM - 2hr - Loading Dock - Team 4 ) show
6 68 moveto
( Load Out: Team 2 - 4pm Sun ) show
9 60 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 2 ) show
6 52 moveto
( Load Out: Team 2 - 6pm Sun ) show
9 44 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 2 ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Jonah ) show
6 132 moveto
( Load In: Team 6 - 6pm Fri ) show
9 124 moveto
( Fri 6:00 PM - 2hr - Loading Dock - Team 6 ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Jordan Mulder-Crouser ) show
6 132 moveto
( Power Play Load In (assisting) ) show
9 124 moveto
( Fri 5:00 PM - 45min - Blackstone ) show
6 116 moveto
( Cameryn Moore:  Power I Play (assisting) ) show
9 108 moveto
( Fri 5:45 PM - 1hr 45min - Blackstone ) show
6 100 moveto
( Power Play Load Out (assisting) ) show
9 92 moveto
( Fri 7:30 PM - 30min - Blackstone ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Justin ) show
6 132 moveto
( Load In: Team 5 - 10am Fri ) show
9 124 moveto
( Fri 10:00 AM - 2hr - Loading Dock - Team 5 ) show
6 116 moveto
( Load In: Team 5 - 12pm Fri ) show
9 108 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 5 ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Kaimi ) show
6 132 moveto
( Load In: Team 3 - 12pm Fri ) show
9 124 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 3 ) show
6 116 moveto
( Load In: Team 3 - 2pm Fri ) show
9 108 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 3 ) show
6 100 moveto
( Doors and Floors - 11am Sat ) show
9 92 moveto
( Sat 11:00 AM - 2hr - Doors-Floors ) show
6 84 moveto
( Load Out: Team 2 - 4pm Sun ) show
9 76 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 2 ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Kalika ) show
6 132 moveto
( Load In: Team 2 - 10am Fri ) show
9 124 moveto
( Fri 10:00 AM - 2hr - Loading Dock - Team 2 ) show
6 116 moveto
( Load Out: Team 1 - 6pm Sun ) show
9 108 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 1 ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Kat ) show
6 132 moveto
( 5th Floor - 9am Sat ) show
9 124 moveto
( Sat 9:00 AM - 2hr - CM-5th Floor ) show
6 116 moveto
( 4th Floor - 3pm Sat ) show
9 108 moveto
( Sat 3:00 PM - 2hr - CM-4th Floor ) show
6 100 moveto
( Wristbander - 9am Sun ) show
9 92 moveto
( Sun 9:00 AM - 2hr - Reg Desk ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Kate ) show
6 132 moveto
( ID Checker - 11am Sun ) show
9 124 moveto
( Sun 11:00 AM - 2hr - Reg Desk-ID ) show
6 116 moveto
( NELA Associates Table - 1pm Sun ) show
9 108 moveto
( Sun 1:00 PM - 2hr - NELA Associates Sales ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Kathryn ) show
6 132 moveto
( Load In: Team 6 - 6pm Fri ) show
9 124 moveto
( Fri 6:00 PM - 2hr - Loading Dock - Team 6 ) show
6 116 moveto
( Doors and Floors - 9am Sat ) show
9 108 moveto
( Sat 9:00 AM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( katie ) show
6 132 moveto
( Volunteer Lounge 1pm Sun ) show
9 124 moveto
( Sun 1:00 PM - 2hr - Boardroom ) show
6 116 moveto
( Load Out: Team 5 - 4pm Sun ) show
9 108 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 5 ) show
6 100 moveto
( Load Out: Team 5 - 6pm Sun ) show
9 92 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 5 ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Katy ) show
6 132 moveto
( Volunteer Lounge 9am Sat ) show
9 124 moveto
( Sat 9:00 AM - 2hr - Boardroom ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( katzelmacher ) show
6 132 moveto
( Dominant as Leader and Ultimate Servant (announcer/inside room attendant) ) show
9 124 moveto
( Sat 2:45 PM - 1hr 30min - 418 ) show
6 116 moveto
( Vaginal and Anal Fisting 101 (announcer/inside room attendant) ) show
9 108 moveto
( Sat 4:30 PM - 1hr 30min - 518 ) show
6 100 moveto
( Liminality of Rope (announcer/inside room attendant) ) show
9 92 moveto
( Sat 6:15 PM - 1hr 30min - 418 ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( KeerBeau ) show
6 132 moveto
( Load Out: Team 2 - 8pm Sun ) show
9 124 moveto
( Sun 8:00 PM - 2hr - Loading Dock - Team 2 ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Kikea ) show
6 132 moveto
( Load In: Team 4 - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 4 ) show
6 116 moveto
( Load In: Team 4 - 6pm Fri ) show
9 108 moveto
( Fri 6:00 PM - 2hr - Loading Dock - Team 4 ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Kim N ) show
6 132 moveto
( Wristband Sales - 4pm Fri (moderating) ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Reg Desk ) show
6 116 moveto
( Wristbander - 6pm Fri ) show
9 108 moveto
( Fri 6:00 PM - 2hr - Reg Desk ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( KissableDom ) show
6 132 moveto
( Wristbander - 9am Sat ) show
9 124 moveto
( Sat 9:00 AM - 1hr - Reg Desk ) show
6 116 moveto
( Volunteer Lounge 11am Sat ) show
9 108 moveto
( Sat 11:00 AM - 2hr - Boardroom ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Kristen ) show
6 132 moveto
( Load Out: Team 4 - 6pm Sun ) show
9 124 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 4 ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Kryssi Bee ) show
6 132 moveto
( Doors and Floors - 9am Sat ) show
9 124 moveto
( Sat 9:00 AM - 2hr - Doors-Floors ) show
6 116 moveto
( Doors and Floors - 3pm Sat ) show
9 108 moveto
( Sat 3:00 PM - 2hr - Doors-Floors ) show
6 100 moveto
( Load Out: Team 5 - 4pm Sun ) show
9 92 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 5 ) show
6 84 moveto
( Load Out: Team 5 - 6pm Sun ) show
9 76 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 5 ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Kyle BostonDSM ) show
6 132 moveto
( MonoPoly (moderating) ) show
9 124 moveto
( Fri 6:15 PM - 1hr 30min - South County ) show
6 116 moveto
( Alternative Activism (moderating) ) show
9 108 moveto
( Sun 1:45 PM - 1hr 30min - 518 ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( La Dresseuse ) show
6 132 moveto
( Pony Paddock (moderating) ) show
9 124 moveto
( Sun 10:00 AM - 6hr - South County ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( La Louve ) show
6 132 moveto
( Load In: Team 3 - 12pm Fri ) show
9 124 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 3 ) show
6 116 moveto
( Load In: Team 3 - 2pm Fri ) show
9 108 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 3 ) show
6 100 moveto
( Load Out: Team 5 - 4pm Sun ) show
9 92 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 5 ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Lady G ) show
6 132 moveto
( Doors and Floors - 2pm Sun ) show
9 124 moveto
( Sun 2:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Lady Shimla ) show
6 132 moveto
( Violet Wands (moderating) ) show
9 124 moveto
( Sat 9:00 AM - 1hr 30min - 518 ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Lady Z ) show
6 132 moveto
( NELA Associates Table - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - NELA Associates Sales ) show
6 116 moveto
( NELA Associates Table - 6pm Fri ) show
9 108 moveto
( Fri 6:00 PM - 2hr - NELA Associates Sales ) show
6 100 moveto
( NELA Associates Table - 12pm Sat ) show
9 92 moveto
( Sat 12:00 PM - 2hr - NELA Associates Sales ) show
6 84 moveto
( NELA Associates Table - 11am Sun ) show
9 76 moveto
( Sun 11:00 AM - 2hr - NELA Associates Sales ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Laura Antoniou ) show
6 132 moveto
( Service with a Smile (moderating) ) show
9 124 moveto
( Fri 6:15 PM - 1hr 30min - 418 ) show
6 116 moveto
( Romantic Surrender (moderating) ) show
9 108 moveto
( Sat 2:45 PM - 1hr 30min - Naragansett C ) show
6 100 moveto
( For your own good (moderating) ) show
9 92 moveto
( Sun 11:45 AM - 1hr 30min - Naragansett C ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Leather By Danny ) show
6 132 moveto
( Saturday Associates Only Vending ) show
9 124 moveto
( Sat 10:30 AM - 30min - Vending ) show
6 116 moveto
( Saturday Vending ) show
9 108 moveto
( Sat 11:00 AM - 7hr - Vending ) show
6 100 moveto
( How to Give/Go to a Play Party ... (moderating) ) show
9 92 moveto
( Sat 11:30 PM - 1hr 30min - 418 ) show
6 84 moveto
( Sunday Associates Only Vending ) show
9 76 moveto
( Sun 10:30 AM - 30min - Vending ) show
6 68 moveto
( Sunday Vending ) show
9 60 moveto
( Sun 11:00 AM - 5hr - Vending ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Lee Harrington ) show
6 132 moveto
( Dark Role Play (moderating) ) show
9 124 moveto
( Sat 10:45 AM - 1hr 30min - Naragansett C ) show
6 116 moveto
( The Call Beyond Equals (moderating) ) show
9 108 moveto
( Sat 6:15 PM - 1hr 30min - Rest. Annex ) show
6 100 moveto
( Beings of Faith and Desire (moderating) ) show
9 92 moveto
( Sat 9:45 PM - 1hr 30min - Blackstone ) show
6 84 moveto
( Spirituality in BDSM (moderating) ) show
9 76 moveto
( Sat 11:30 PM - 1hr 30min - South County ) show
6 68 moveto
( Laughing Our Way to Intimacy: Humor and Sex  (moderating) ) show
9 60 moveto
( Sun 10:00 AM - 1hr 30min - Naragansett A ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Legionnaire06  ) show
6 132 moveto
( Applied Rigging for Brain and Hands (outside wristband checker) ) show
9 124 moveto
( Fri 9:45 PM - 1hr 30min - South County ) show
6 116 moveto
( Beings of Faith and Desire (announcer/inside room attendant) ) show
9 108 moveto
( Sat 9:45 PM - 1hr 30min - Blackstone ) show
6 100 moveto
( Erogenous Zoning Violations (announcer/inside room attendant) ) show
9 92 moveto
( Sat 11:30 PM - 1hr 30min - Blackstone ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Lex ) show
6 132 moveto
( ID Checker - 6pm Fri ) show
9 124 moveto
( Fri 6:00 PM - 2hr - Reg Desk-ID ) show
6 116 moveto
( Doors and Floors - 1pm Sat ) show
9 108 moveto
( Sat 1:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( lilone27 ) show
6 132 moveto
( Load Out: Team 4 - 4pm Sun ) show
9 124 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 4 ) show
6 116 moveto
( Load Out: Team 4 - 6pm Sun ) show
9 108 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 4 ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Lisa ) show
6 132 moveto
( Load In: Team 2 - 2pm Fri ) show
9 124 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 2 ) show
6 116 moveto
( Wristbander - 2pm Sat ) show
9 108 moveto
( Sat 2:00 PM - 2hr - Reg Desk ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Lochai ) show
6 132 moveto
( Fuck Dogma (moderating) ) show
9 124 moveto
( Sat 1:00 PM - 1hr 30min - Naragansett C ) show
6 116 moveto
( Whose Kink is it Anyway? ) show
9 108 moveto
( Sat 4:30 PM - 1hr 30min - Naragansett A ) show
6 100 moveto
( NOT your Grandma's Enema (moderating) ) show
9 92 moveto
( Sat 8:00 PM - 1hr 30min - 418 ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Locke ) show
6 132 moveto
( Load In: Team 2 - 10am Fri ) show
9 124 moveto
( Fri 10:00 AM - 2hr - Loading Dock - Team 2 ) show
6 116 moveto
( Load In: Team 3 - 8pm Fri ) show
9 108 moveto
( Fri 8:00 PM - 2hr - Loading Dock - Team 3 ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( LoMevina ) show
6 132 moveto
( Kabalat Shabbat (moderating) ) show
9 124 moveto
( Fri 4:30 PM - 1hr 30min - 418 ) show
6 116 moveto
( Havdalah (moderating) ) show
9 108 moveto
( Sat 6:15 PM - 1hr 30min - 518 ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Lori ) show
6 132 moveto
( Load In: Team 1 - 12pm Fri ) show
9 124 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 1 ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Louise ) show
6 132 moveto
( Load In: Team 4 - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 4 ) show
6 116 moveto
( 5th Floor - 9am Sat ) show
9 108 moveto
( Sat 9:00 AM - 2hr - CM-5th Floor ) show
6 100 moveto
( NELA Associates Table - 9am Sun ) show
9 92 moveto
( Sun 9:00 AM - 2hr - NELA Associates Sales ) show
6 84 moveto
( Load Out: Team 1 - 4pm Sun ) show
9 76 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 1 ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( LqqkOut ) show
6 132 moveto
( Go Team Slutty! ) show
9 124 moveto
( Sat 1:00 PM - 1hr 30min - Naragansett B ) show
6 116 moveto
( Whose Kink is it Anyway? ) show
9 108 moveto
( Sat 4:30 PM - 1hr 30min - Naragansett A ) show
6 100 moveto
( Discover your Queer Identity (moderating) ) show
9 92 moveto
( Sun 10:00 AM - 1hr 30min - 418 ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Lynn ) show
6 132 moveto
( Wristbander - 4pm Sat ) show
9 124 moveto
( Sat 4:00 PM - 2hr - Reg Desk ) show
6 116 moveto
( Doors and Floors - 2pm Sun ) show
9 108 moveto
( Sun 2:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( M & M Peanut ) show
6 132 moveto
( Doors and Floors - 1pm Sat ) show
9 124 moveto
( Sat 1:00 PM - 2hr - Doors-Floors ) show
6 116 moveto
( Doors and Floors - 3pm Sat ) show
9 108 moveto
( Sat 3:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( M & M plain ) show
6 132 moveto
( Doors and Floors - 1pm Sat ) show
9 124 moveto
( Sat 1:00 PM - 2hr - Doors-Floors ) show
6 116 moveto
( Doors and Floors - 3pm Sat ) show
9 108 moveto
( Sat 3:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Mad Patter ) show
6 132 moveto
( Discovering Your Inner Sadist (announcer/inside room attendant) ) show
9 124 moveto
( Fri 8:00 PM - 1hr 30min - 418 ) show
6 116 moveto
( Interrogation and Military Scenes (announcer/inside room attendant) ) show
9 108 moveto
( Fri 9:45 PM - 1hr 30min - Blackstone ) show
6 100 moveto
( TNG Meet & Greet ) show
9 92 moveto
( Sat 6:00 PM - 1hr - Pres. Suite ) show
6 84 moveto
( Age Play (announcer/inside room attendant) ) show
9 76 moveto
( Sat 8:00 PM - 1hr 30min - Rest. Annex ) show
6 68 moveto
( Power and Authority Exchange Relationships ) show
9 60 moveto
( Sat 11:30 PM - 1hr 30min - 518 ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Madam Tsigane ) show
6 132 moveto
( Doors and Floors - 10am Sun ) show
9 124 moveto
( Sun 10:00 AM - 2hr - Doors-Floors ) show
6 116 moveto
( Doors and Floors - 12pm Sun ) show
9 108 moveto
( Sun 12:00 PM - 2hr - Doors-Floors ) show
6 100 moveto
( Doors and Floors - 2pm Sun ) show
9 92 moveto
( Sun 2:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Madelyn (Miss Wyld) ) show
6 132 moveto
( Volunteer Coordinator - 10am Fri ) show
9 124 moveto
( Fri 10:00 AM - 1hr - Volunteer Coordinator ) show
6 116 moveto
( Volunteer Coordinator - 11am Fri ) show
9 108 moveto
( Fri 11:00 AM - 1hr - Volunteer Coordinator ) show
6 100 moveto
( Volunteer Coordinator - noon Fri ) show
9 92 moveto
( Fri 12:00 PM - 1hr - Volunteer Coordinator ) show
6 84 moveto
( Volunteer Coordinator - 1pm Fri ) show
9 76 moveto
( Fri 1:00 PM - 1hr - Volunteer Coordinator ) show
6 68 moveto
( Volunteer Coordinator - 2pm Fri ) show
9 60 moveto
( Fri 2:00 PM - 1hr - Volunteer Coordinator ) show
6 52 moveto
( Volunteer Coordinator - 3pm Fri ) show
9 44 moveto
( Fri 3:00 PM - 1hr - Volunteer Coordinator ) show
6 36 moveto
( Volunteer Coordinator - 4pm Fri ) show
9 28 moveto
( Fri 4:00 PM - 1hr - Volunteer Coordinator ) show
6 20 moveto
( Volunteer Coordinator - 9am Sat ) show
9 12 moveto
( Sat 9:00 AM - 1hr - Volunteer Coordinator ) show
6 4 moveto
( Volunteer Coordinator - 10am Sat ) show
9 -4 moveto
( Sat 10:00 AM - 1hr - Volunteer Coordinator ) show
6 -12 moveto
( Volunteer Coordinator - 11am Sat ) show
9 -20 moveto
( Sat 11:00 AM - 1hr - Volunteer Coordinator ) show
6 -28 moveto
( Volunteer Coordinator - noon Sat ) show
9 -36 moveto
( Sat 12:00 PM - 1hr - Volunteer Coordinator ) show
6 -44 moveto
( Volunteer Coordinator - 1pm Sat ) show
9 -52 moveto
( Sat 1:00 PM - 1hr - Volunteer Coordinator ) show
6 -60 moveto
( VASE Meetup (moderating) ) show
9 -68 moveto
( Sat 4:00 PM - 2hr - Pres. Suite ) show
6 -76 moveto
( Bi/Poly/Switch Panel ) show
9 -84 moveto
( Sat 11:30 PM - 1hr 30min - Rest. Annex ) show
6 -92 moveto
( Volunteer Lounge 9am Sun ) show
9 -100 moveto
( Sun 9:00 AM - 2hr - Boardroom ) show
6 -108 moveto
( Volunteer Coordinator - 2pm Sun ) show
9 -116 moveto
( Sun 2:00 PM - 1hr - Volunteer Coordinator ) show
6 -124 moveto
( Volunteer Coordinator - 3pm Sun ) show
9 -132 moveto
( Sun 3:00 PM - 1hr - Volunteer Coordinator ) show
6 -140 moveto
( Volunteer Coordinator - 4pm Sun ) show
9 -148 moveto
( Sun 4:00 PM - 1hr - Volunteer Coordinator ) show
6 -156 moveto
( Volunteer Coordinator - 5pm Sun ) show
9 -164 moveto
( Sun 5:00 PM - 1hr - Volunteer Coordinator ) show
6 -172 moveto
( Volunteer Coordinator - 6pm Sun ) show
9 -180 moveto
( Sun 6:00 PM - 1hr - Volunteer Coordinator ) show
6 -188 moveto
( Volunteer Coordinator - 7pm Sun ) show
9 -196 moveto
( Sun 7:00 PM - 1hr - Volunteer Coordinator ) show
6 -204 moveto
( Volunteer Coordinator - 8pm Sun ) show
9 -212 moveto
( Sun 8:00 PM - 1hr - Volunteer Coordinator ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( MadLady ) show
6 132 moveto
( ID Checker - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Reg Desk-ID ) show
6 116 moveto
( Load In: Team 6 - 6pm Fri ) show
9 108 moveto
( Fri 6:00 PM - 2hr - Loading Dock - Team 6 ) show
6 100 moveto
( Wristbander - 9am Sat ) show
9 92 moveto
( Sat 9:00 AM - 1hr - Reg Desk ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Marceline ) show
6 132 moveto
( ID Checker - 10am Sat ) show
9 124 moveto
( Sat 10:00 AM - 2hr - Reg Desk-ID ) show
6 116 moveto
( Wristbander - 9am Sun ) show
9 108 moveto
( Sun 9:00 AM - 2hr - Reg Desk ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Maria ) show
6 132 moveto
( Load In: Team 2 - 2pm Fri ) show
9 124 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 2 ) show
6 116 moveto
( Volunteer Lounge 9am Sat ) show
9 108 moveto
( Sat 9:00 AM - 2hr - Boardroom ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Marie ) show
6 132 moveto
( Wristbander - 2pm Sat ) show
9 124 moveto
( Sat 2:00 PM - 2hr - Reg Desk ) show
6 116 moveto
( Doors and Floors - 2pm Sun ) show
9 108 moveto
( Sun 2:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Mark ) show
6 132 moveto
( Load In: Team 3 - 12pm Fri ) show
9 124 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 3 ) show
6 116 moveto
( Load In: Team 3 - 2pm Fri ) show
9 108 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 3 ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Marko ) show
6 132 moveto
( Wristbander - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Reg Desk ) show
6 116 moveto
( Wristbander - 6pm Fri ) show
9 108 moveto
( Fri 6:00 PM - 2hr - Reg Desk ) show
6 100 moveto
( Volunteer Lounge 8pm Fri ) show
9 92 moveto
( Fri 8:00 PM - 2hr - Boardroom ) show
6 84 moveto
( Volunteer Lounge 3pm Sat ) show
9 76 moveto
( Sat 3:00 PM - 2hr - Boardroom ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Mary ) show
6 132 moveto
( Doors and Floors - 9am Sat ) show
9 124 moveto
( Sat 9:00 AM - 2hr - Doors-Floors ) show
6 116 moveto
( Wristbander - 4pm Sat ) show
9 108 moveto
( Sat 4:00 PM - 2hr - Reg Desk ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( MattzBattz ) show
6 132 moveto
( Uniform fetish from the Mundane to the Military  (announcer/inside room attendant) ) show
9 124 moveto
( Sat 9:00 AM - 1hr 30min - Naragansett C ) show
6 116 moveto
( Coming Out Kinky (announcer/inside room attendant) ) show
9 108 moveto
( Sat 2:45 PM - 1hr 30min - Rest. Annex ) show
6 100 moveto
( Author Readings (announcer/inside room attendant) ) show
9 92 moveto
( Sat 8:00 PM - 1hr 30min - 518 ) show
6 84 moveto
( Power and Authority Exchange Relationships (announcer/inside room attendant) ) show
9 76 moveto
( Sat 11:30 PM - 1hr 30min - 518 ) show
6 68 moveto
( Aural Sex : Seduction by Voice &  Story Telling  (outside wristband checker) ) show
9 60 moveto
( Sun 1:45 PM - 1hr 30min - Naragansett C ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( MBob ) show
6 132 moveto
( Load In: Team 6 - 10am Fri ) show
9 124 moveto
( Fri 10:00 AM - 2hr - Loading Dock - Team 6 ) show
6 116 moveto
( Load In: Team 6 - 12pm Fri ) show
9 108 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 6 ) show
6 100 moveto
( Load In: Team 6 - 2pm Fri ) show
9 92 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 6 ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Mel ) show
6 132 moveto
( ID Checker - 4pm Sat ) show
9 124 moveto
( Sat 4:00 PM - 2hr - Reg Desk-ID ) show
6 116 moveto
( ID Checker - 11am Sun ) show
9 108 moveto
( Sun 11:00 AM - 2hr - Reg Desk-ID ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( melissa ) show
6 132 moveto
( Volunteer Lounge 11am Sun ) show
9 124 moveto
( Sun 11:00 AM - 2hr - Boardroom ) show
6 116 moveto
( Volunteer Lounge 1pm Sun ) show
9 108 moveto
( Sun 1:00 PM - 2hr - Boardroom ) show
6 100 moveto
( Volunteer Lounge 3pm Sun ) show
9 92 moveto
( Sun 3:00 PM - 2hr - Boardroom ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( mely ) show
6 132 moveto
( Transgender Spirituality (announcer/inside room attendant) ) show
9 124 moveto
( Fri 9:45 PM - 1hr 30min - 518 ) show
6 116 moveto
( Violet Wands (announcer/inside room attendant) ) show
9 108 moveto
( Sat 9:00 AM - 1hr 30min - 518 ) show
6 100 moveto
( Negotiating for Submissives (announcer/inside room attendant) ) show
9 92 moveto
( Sat 4:30 PM - 1hr 30min - Naragansett C ) show
6 84 moveto
( Compassionate Communication (announcer/inside room attendant) ) show
9 76 moveto
( Sat 6:15 PM - 1hr 30min - Blackstone ) show
6 68 moveto
( Bi/Poly/Switch Panel (announcer/inside room attendant) ) show
9 60 moveto
( Sat 11:30 PM - 1hr 30min - Rest. Annex ) show
6 52 moveto
( Playing with the Pussy (announcer/inside room attendant) ) show
9 44 moveto
( Sun 10:00 AM - 1hr 30min - Naragansett B ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Mephki ) show
6 132 moveto
( New England Hypnosis NELA SIG (moderating) ) show
9 124 moveto
( Fri 9:00 PM - 1hr - Pres. Suite ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Michael S ) show
6 132 moveto
( Doors and Floors - 3pm Sat ) show
9 124 moveto
( Sat 3:00 PM - 2hr - Doors-Floors ) show
6 116 moveto
( Doors and Floors - 5pm Sat ) show
9 108 moveto
( Sat 5:00 PM - 2hr - Doors-Floors ) show
6 100 moveto
( Volunteer Lounge 1pm Sun ) show
9 92 moveto
( Sun 1:00 PM - 2hr - Boardroom ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Michele ) show
6 132 moveto
( Connection and Intent (moderating) ) show
9 124 moveto
( Sat 9:45 PM - 1hr 30min - South County ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Midori ) show
6 132 moveto
( Interrogation and Military Scenes (moderating) ) show
9 124 moveto
( Fri 9:45 PM - 1hr 30min - Blackstone ) show
6 116 moveto
( Erotic Humiliation Play (moderating) ) show
9 108 moveto
( Sat 2:45 PM - 1hr 30min - Naragansett A ) show
6 100 moveto
( Aural Sex : Seduction by Voice &  Story Telling  (moderating) ) show
9 92 moveto
( Sun 1:45 PM - 1hr 30min - Naragansett C ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Misfit Mike ) show
6 132 moveto
( ID Checker - 8pm Fri ) show
9 124 moveto
( Fri 8:00 PM - 2hr - Reg Desk-ID ) show
6 116 moveto
( Doors and Floors - 11am Sat ) show
9 108 moveto
( Sat 11:00 AM - 2hr - Doors-Floors ) show
6 100 moveto
( ID Checker - 2pm Sat ) show
9 92 moveto
( Sat 2:00 PM - 2hr - Reg Desk-ID ) show
6 84 moveto
( ID Checker - 4pm Sat ) show
9 76 moveto
( Sat 4:00 PM - 2hr - Reg Desk-ID ) show
6 68 moveto
( Doors and Floors - 2pm Sun ) show
9 60 moveto
( Sun 2:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Mishi ) show
6 132 moveto
( Load Out: Team 3 - 4pm Sun ) show
9 124 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 3 ) show
6 116 moveto
( Load Out: Team 3 - 6pm Sun ) show
9 108 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 3 ) show
6 100 moveto
( Load Out: Team 3 - 8pm Sun ) show
9 92 moveto
( Sun 8:00 PM - 2hr - Loading Dock - Team 3 ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Miss Aurora ) show
6 132 moveto
( Disability Assistance - 6pm Fri ) show
9 124 moveto
( Fri 6:00 PM - 2hr - ADA ) show
6 116 moveto
( Disability Assistance - 1pm Sat ) show
9 108 moveto
( Sat 1:00 PM - 2hr - ADA ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Miss Cindy ) show
6 132 moveto
( Femdom Panel (moderating) ) show
9 124 moveto
( Sat 10:45 AM - 1hr 30min - Pres. Suite ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Miss Luna ) show
6 132 moveto
( Femme (R)evolution (moderating) ) show
9 124 moveto
( Fri 9:45 PM - 1hr 30min - 418 ) show
6 116 moveto
( Age play BOF (moderating) ) show
9 108 moveto
( Sat 3:00 PM - 1hr - Pres. Suite ) show
6 100 moveto
( Age Play (moderating) ) show
9 92 moveto
( Sat 8:00 PM - 1hr 30min - Rest. Annex ) show
6 84 moveto
( Age Play Panel ) show
9 76 moveto
( Sat 9:45 PM - 1hr 30min - 518 ) show
6 68 moveto
( Bootblacking 101 (moderating) ) show
9 60 moveto
( Sun 11:45 AM - 1hr 30min - 418 ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Mistress Thorne ) show
6 132 moveto
( ID Checker - 12pm Sat ) show
9 124 moveto
( Sat 12:00 PM - 2hr - Reg Desk-ID ) show
6 116 moveto
( 5th Floor - 10am Sun ) show
9 108 moveto
( Sun 10:00 AM - 2hr - CM-5th Floor ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Mistress Tink ) show
6 132 moveto
( Volunteer Lounge 2pm Fri (moderating) ) show
9 124 moveto
( Fri 2:00 PM - 2hr - Boardroom ) show
6 116 moveto
( Volunteer Lounge 4pm Fri ) show
9 108 moveto
( Fri 4:00 PM - 2hr - Boardroom ) show
6 100 moveto
( Volunteer Lounge 3pm Sun ) show
9 92 moveto
( Sun 3:00 PM - 2hr - Boardroom ) show
6 84 moveto
( Volunteer Lounge 5pm Sun ) show
9 76 moveto
( Sun 5:00 PM - 2hr - Boardroom ) show
6 68 moveto
( Volunteer Lounge 7pm Sun ) show
9 60 moveto
( Sun 7:00 PM - 2hr - Boardroom ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( MistressTasteful ) show
6 132 moveto
( TwoPlay: The Art of Making Out (outside wristband checker) ) show
9 124 moveto
( Fri 8:00 PM - 1hr 30min - Blackstone ) show
6 116 moveto
( Femme (R)evolution (announcer/inside room attendant) ) show
9 108 moveto
( Fri 9:45 PM - 1hr 30min - 418 ) show
6 100 moveto
( Erotic Hypnosis 101 with DrSlashBlight (outside wristband checker) ) show
9 92 moveto
( Sat 8:00 PM - 1hr 30min - Blackstone ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Mizzy ) show
6 132 moveto
( Volunteer Lounge 9am Sun ) show
9 124 moveto
( Sun 9:00 AM - 2hr - Boardroom ) show
6 116 moveto
( Doors and Floors - 12pm Sun ) show
9 108 moveto
( Sun 12:00 PM - 2hr - Doors-Floors ) show
6 100 moveto
( Doors and Floors - 2pm Sun ) show
9 92 moveto
( Sun 2:00 PM - 2hr - Doors-Floors ) show
6 84 moveto
( Volunteer Lounge 5pm Sun ) show
9 76 moveto
( Sun 5:00 PM - 2hr - Boardroom ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Mollena ) show
6 132 moveto
( Growing together through being apart (moderating) ) show
9 124 moveto
( Sat 10:45 AM - 1hr 30min - Naragansett B ) show
6 116 moveto
( Whose Kink is it Anyway? ) show
9 108 moveto
( Sat 4:30 PM - 1hr 30min - Naragansett A ) show
6 100 moveto
( Strong Slaves, Bodacious Bottoms (moderating) ) show
9 92 moveto
( Sat 8:00 PM - 1hr 30min - South County ) show
6 84 moveto
( Monogamy Without Shame (moderating) ) show
9 76 moveto
( Sun 11:45 AM - 1hr 30min - Rest. Annex ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Molly ) show
6 132 moveto
( Load In: Team 4 - 6pm Fri ) show
9 124 moveto
( Fri 6:00 PM - 2hr - Loading Dock - Team 4 ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Monique ) show
6 132 moveto
( Load In: Team 5 - 10am Fri ) show
9 124 moveto
( Fri 10:00 AM - 2hr - Loading Dock - Team 5 ) show
6 116 moveto
( Load In: Team 5 - 12pm Fri ) show
9 108 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 5 ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( moonlight ) show
6 132 moveto
( Volunteer Lounge noon Fri ) show
9 124 moveto
( Fri 12:00 PM - 2hr - Boardroom ) show
6 116 moveto
( Volunteer Lounge 2pm Fri ) show
9 108 moveto
( Fri 2:00 PM - 2hr - Boardroom ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Mr. M ) show
6 132 moveto
( Load Out: Team 1 - 4pm Sun ) show
9 124 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 1 ) show
6 116 moveto
( Load Out: Team 1 - 6pm Sun ) show
9 108 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 1 ) show
6 100 moveto
( Load Out: Team 1 - 8pm Sun ) show
9 92 moveto
( Sun 8:00 PM - 2hr - Loading Dock - Team 1 ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Mr. Powertie ) show
6 132 moveto
( Load Out: Team 4 - 4pm Sun ) show
9 124 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 4 ) show
6 116 moveto
( Load Out: Team 4 - 6pm Sun ) show
9 108 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 4 ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Ms. Leading ) show
6 132 moveto
( Load Out: Team 5 - 4pm Sun ) show
9 124 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 5 ) show
6 116 moveto
( Load Out: Team 5 - 6pm Sun ) show
9 108 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 5 ) show
6 100 moveto
( Load Out: Team 5 - 8pm Sun ) show
9 92 moveto
( Sun 8:00 PM - 2hr - Loading Dock - Team 5 ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Mud ) show
6 132 moveto
( ID Checker - 2pm Sat ) show
9 124 moveto
( Sat 2:00 PM - 2hr - Reg Desk-ID ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Murphy Blue ) show
6 132 moveto
( Discovering Your Inner Sadist (moderating) ) show
9 124 moveto
( Fri 8:00 PM - 1hr 30min - 418 ) show
6 116 moveto
( Introduction to Rope Bondage (moderating) ) show
9 108 moveto
( Sat 10:45 AM - 1hr 30min - Rest. Annex ) show
6 100 moveto
( Arm Bindings (moderating) ) show
9 92 moveto
( Sun 10:00 AM - 1hr 30min - Naragansett C ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Mystress Autumn ) show
6 132 moveto
( Doors and Floors - 10am Sun ) show
9 124 moveto
( Sun 10:00 AM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Natella ) show
6 132 moveto
( 4th Floor - 10am Sun ) show
9 124 moveto
( Sun 10:00 AM - 2hr - CM-4th Floor ) show
6 116 moveto
( 4th Floor - 12pm Sun ) show
9 108 moveto
( Sun 12:00 PM - 2hr - CM-4th Floor ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( nathaniel ) show
6 132 moveto
( Sex Positive: What does it mean? (announcer/inside room attendant) ) show
9 124 moveto
( Sat 1:00 PM - 1hr 30min - Rest. Annex ) show
6 116 moveto
( The Sensuous Art of Caning (announcer/inside room attendant) ) show
9 108 moveto
( Sat 6:15 PM - 1hr 30min - South County ) show
6 100 moveto
( Beings of Faith and Desire (outside wristband checker) ) show
9 92 moveto
( Sat 9:45 PM - 1hr 30min - Blackstone ) show
6 84 moveto
( Guerrilla Fetish Photography and the Law (announcer/inside room attendant) ) show
9 76 moveto
( Sun 1:45 PM - 1hr 30min - Rest. Annex ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( NaughtyBaby ) show
6 132 moveto
( Switches BOF (moderating) ) show
9 124 moveto
( Sat 1:00 PM - 1hr - Pres. Suite ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( NaughtyEm ) show
6 132 moveto
( Go Team Slutty! (announcer/inside room attendant) ) show
9 124 moveto
( Sat 1:00 PM - 1hr 30min - Naragansett B ) show
6 116 moveto
( Elemental One Hand Clap (moderating) ) show
9 108 moveto
( Sat 2:45 PM - 1hr 30min - 518 ) show
6 100 moveto
( Spanking BOF (moderating) ) show
9 92 moveto
( Sat 9:00 PM - 1hr - Pres. Suite ) show
6 84 moveto
( Bi/Poly/Switch Panel ) show
9 76 moveto
( Sat 11:30 PM - 1hr 30min - Rest. Annex ) show
6 68 moveto
( Discover your Queer Identity (announcer/inside room attendant) ) show
9 60 moveto
( Sun 10:00 AM - 1hr 30min - 418 ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( NaughtyRalphie ) show
6 132 moveto
( Switches BOF ) show
9 124 moveto
( Sat 1:00 PM - 1hr - Pres. Suite ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( NELA Volunteer ) show
6 132 moveto
( Whip Lounge (outside wristband checker) ) show
9 124 moveto
( Sat 10:30 AM - 7hr 30min - South County ) show
6 116 moveto
( Roper Room (outside wristband checker) ) show
9 108 moveto
( Sat 10:30 AM - 7hr 30min - Blackstone ) show
6 100 moveto
( Introduction to Rope Bondage (outside wristband checker) ) show
9 92 moveto
( Sat 10:45 AM - 1hr 30min - Rest. Annex ) show
6 84 moveto
( Rope Bottom: An Advanced Guide for Frequent Flyer (outside wristband checker) ) show
9 76 moveto
( Sat 10:45 AM - 1hr 30min - 418 ) show
6 68 moveto
( Go Team Slutty! (outside wristband checker) ) show
9 60 moveto
( Sat 1:00 PM - 1hr 30min - Naragansett B ) show
6 52 moveto
( Coral and the Chocolate Fetish (outside wristband checker) ) show
9 44 moveto
( Sat 1:00 PM - 1hr 30min - 418 ) show
6 36 moveto
( Sex Positive: What does it mean? (outside wristband checker) ) show
9 28 moveto
( Sat 1:00 PM - 1hr 30min - Rest. Annex ) show
6 20 moveto
( Mommy Dearest (outside wristband checker) ) show
9 12 moveto
( Sat 1:00 PM - 1hr 30min - 518 ) show
6 4 moveto
( Elemental One Hand Clap (outside wristband checker) ) show
9 -4 moveto
( Sat 2:45 PM - 1hr 30min - 518 ) show
6 -12 moveto
( Coming Out Kinky (outside wristband checker) ) show
9 -20 moveto
( Sat 2:45 PM - 1hr 30min - Rest. Annex ) show
6 -28 moveto
( Dominant as Leader and Ultimate Servant (outside wristband checker) ) show
9 -36 moveto
( Sat 2:45 PM - 1hr 30min - 418 ) show
6 -44 moveto
( Vaginal and Anal Fisting 101 (outside wristband checker) ) show
9 -52 moveto
( Sat 4:30 PM - 1hr 30min - 518 ) show
6 -60 moveto
( Fucking With Your Camera (outside wristband checker) ) show
9 -68 moveto
( Sat 4:30 PM - 1hr 30min - 418 ) show
6 -76 moveto
( Playing with Saran Wrap (outside wristband checker) ) show
9 -84 moveto
( Sat 4:30 PM - 1hr 30min - Rest. Annex ) show
6 -92 moveto
( Pony Paddock (outside wristband checker) ) show
9 -100 moveto
( Sun 10:00 AM - 6hr - South County ) show
6 -108 moveto
( Improvisational Skills for Role Play  (outside wristband checker) ) show
9 -116 moveto
( Sun 10:00 AM - 1hr 30min - Rest. Annex ) show
6 -124 moveto
( Catheters and Sounds (outside wristband checker) ) show
9 -132 moveto
( Sun 10:00 AM - 1hr 30min - 518 ) show
6 -140 moveto
( Discover your Queer Identity (outside wristband checker) ) show
9 -148 moveto
( Sun 10:00 AM - 1hr 30min - 418 ) show
6 -156 moveto
( Photography Exhibit (outside wristband checker) ) show
9 -164 moveto
( Sun 10:00 AM - 6hr - Blackstone ) show
6 -172 moveto
( Bootblacking 101 (outside wristband checker) ) show
9 -180 moveto
( Sun 11:45 AM - 1hr 30min - 418 ) show
6 -188 moveto
( Monogamy Without Shame (outside wristband checker) ) show
9 -196 moveto
( Sun 11:45 AM - 1hr 30min - Rest. Annex ) show
6 -204 moveto
( Getting Good Head (outside wristband checker) ) show
9 -212 moveto
( Sun 11:45 AM - 1hr 30min - 518 ) show
6 -220 moveto
( Guerrilla Fetish Photography and the Law (outside wristband checker) ) show
9 -228 moveto
( Sun 1:45 PM - 1hr 30min - Rest. Annex ) show
6 -236 moveto
( Alternative Activism (outside wristband checker) ) show
9 -244 moveto
( Sun 1:45 PM - 1hr 30min - 518 ) show
6 -252 moveto
( You're doing it wrong (outside wristband checker) ) show
9 -260 moveto
( Sun 1:45 PM - 1hr 30min - 418 ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( NeoClassic ) show
6 132 moveto
( Puppy Play with Rabbit (outside wristband checker) ) show
9 124 moveto
( Fri 6:15 PM - 1hr 30min - 518 ) show
6 116 moveto
( Scene Survival for New Players (announcer/inside room attendant) ) show
9 108 moveto
( Fri 8:00 PM - 1hr 30min - Rest. Annex ) show
6 100 moveto
( Interrogation and Military Scenes (outside wristband checker) ) show
9 92 moveto
( Fri 9:45 PM - 1hr 30min - Blackstone ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( NH Kinkster Couple ) show
6 132 moveto
( Doors and Floors - 11am Sat ) show
9 124 moveto
( Sat 11:00 AM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( NH Rope Slut ) show
6 132 moveto
( Volunteer Lounge 11am Sat ) show
9 124 moveto
( Sat 11:00 AM - 2hr - Boardroom ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Nik ) show
6 132 moveto
( Volunteer Lounge 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Boardroom ) show
6 116 moveto
( Volunteer Lounge 6pm Fri ) show
9 108 moveto
( Fri 6:00 PM - 2hr - Boardroom ) show
6 100 moveto
( Wristbander - 10am Sat ) show
9 92 moveto
( Sat 10:00 AM - 2hr - Reg Desk ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Nutmeg ) show
6 132 moveto
( Load In: Team 2 - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 2 ) show
6 116 moveto
( Load In: Team 2 - 6pm Fri ) show
9 108 moveto
( Fri 6:00 PM - 2hr - Loading Dock - Team 2 ) show
6 100 moveto
( 4th Floor - 12pm Sun ) show
9 92 moveto
( Sun 12:00 PM - 2hr - CM-4th Floor ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Nymeria ) show
6 132 moveto
( Yoga for Kinksters (announcer/inside room attendant) ) show
9 124 moveto
( Sat 10:45 AM - 1hr 30min - Naragansett A ) show
6 116 moveto
( So You Like it Rough? (announcer/inside room attendant) ) show
9 108 moveto
( Sat 1:00 PM - 1hr 30min - Naragansett A ) show
6 100 moveto
( Alternative Activism (announcer/inside room attendant) ) show
9 92 moveto
( Sun 1:45 PM - 1hr 30min - 518 ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Opn ) show
6 132 moveto
( Fleadom Trail Friday (moderating) ) show
9 124 moveto
( Fri 5:30 PM - 1hr 30min - Rest. Annex ) show
6 116 moveto
( Fleadom Trail - Saturday (moderating) ) show
9 108 moveto
( Sat 9:30 AM - 1hr 15min - Rest. Annex ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Oro ) show
6 132 moveto
( Load In: Team 5 - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 5 ) show
6 116 moveto
( Load In: Team 5 - 6pm Fri ) show
9 108 moveto
( Fri 6:00 PM - 2hr - Loading Dock - Team 5 ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( P.E.T.E.! ) show
6 132 moveto
( Universal and Sensual Foot Experience (moderating) ) show
9 124 moveto
( Sat 9:00 AM - 1hr 30min - Naragansett A ) show
6 116 moveto
( Whose Kink is it Anyway? ) show
9 108 moveto
( Sat 4:30 PM - 1hr 30min - Naragansett A ) show
6 100 moveto
( Tickle Play with P.E.T.E (moderating) ) show
9 92 moveto
( Sun 1:45 PM - 1hr 30min - Naragansett B ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Padhana ) show
6 132 moveto
( Doors and Floors - 5pm Sat ) show
9 124 moveto
( Sat 5:00 PM - 2hr - Doors-Floors ) show
6 116 moveto
( Volunteer Lounge 3pm Sun ) show
9 108 moveto
( Sun 3:00 PM - 2hr - Boardroom ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( PandaPet ) show
6 132 moveto
( Service & Devotion: Love in a Power Dynamic (announcer/inside room attendant) ) show
9 124 moveto
( Fri 8:00 PM - 1hr 30min - 518 ) show
6 116 moveto
( Rope Bottom: An Advanced Guide for Frequent Flyer (announcer/inside room attendant) ) show
9 108 moveto
( Sat 10:45 AM - 1hr 30min - 418 ) show
6 100 moveto
( Coral and the Chocolate Fetish (announcer/inside room attendant) ) show
9 92 moveto
( Sat 1:00 PM - 1hr 30min - 418 ) show
6 84 moveto
( Laughing Our Way to Intimacy: Humor and Sex  (outside wristband checker) ) show
9 76 moveto
( Sun 10:00 AM - 1hr 30min - Naragansett A ) show
6 68 moveto
( Setting the Scene and Building Trust (outside wristband checker) ) show
9 60 moveto
( Sun 11:45 AM - 1hr 30min - Naragansett A ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Parks ) show
6 132 moveto
( Volunteer Lounge 6pm Fri ) show
9 124 moveto
( Fri 6:00 PM - 2hr - Boardroom ) show
6 116 moveto
( Volunteer Lounge 9am Sat ) show
9 108 moveto
( Sat 9:00 AM - 2hr - Boardroom ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Paul A ) show
6 132 moveto
( Doors and Floors - 9am Sat ) show
9 124 moveto
( Sat 9:00 AM - 2hr - Doors-Floors ) show
6 116 moveto
( Doors and Floors - 11am Sat ) show
9 108 moveto
( Sat 11:00 AM - 2hr - Doors-Floors ) show
6 100 moveto
( Doors and Floors - 1pm Sat ) show
9 92 moveto
( Sat 1:00 PM - 2hr - Doors-Floors ) show
6 84 moveto
( Doors and Floors - 10am Sun ) show
9 76 moveto
( Sun 10:00 AM - 2hr - Doors-Floors ) show
6 68 moveto
( Doors and Floors - 12pm Sun ) show
9 60 moveto
( Sun 12:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Paul C ) show
6 132 moveto
( Load In: Team 2 - 12pm Fri ) show
9 124 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 2 ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Peaches ) show
6 132 moveto
( Volunteer Lounge 1pm Sat ) show
9 124 moveto
( Sat 1:00 PM - 2hr - Boardroom ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Pencildragon ) show
6 132 moveto
( Universal and Sensual Foot Experience (outside wristband checker) ) show
9 124 moveto
( Sat 9:00 AM - 1hr 30min - Naragansett A ) show
6 116 moveto
( Yoga for Kinksters (outside wristband checker) ) show
9 108 moveto
( Sat 10:45 AM - 1hr 30min - Naragansett A ) show
6 100 moveto
( The Call Beyond Equals (outside wristband checker) ) show
9 92 moveto
( Sat 6:15 PM - 1hr 30min - Rest. Annex ) show
6 84 moveto
( Age Play (outside wristband checker) ) show
9 76 moveto
( Sat 8:00 PM - 1hr 30min - Rest. Annex ) show
6 68 moveto
( Poly 101 (outside wristband checker) ) show
9 60 moveto
( Sat 9:45 PM - 1hr 30min - Rest. Annex ) show
6 52 moveto
( Erogenous Zoning Violations (outside wristband checker) ) show
9 44 moveto
( Sat 11:30 PM - 1hr 30min - Blackstone ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( pet tigress ) show
6 132 moveto
( Load In: Team 1 - 12pm Fri ) show
9 124 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 1 ) show
6 116 moveto
( Load In: Team 1 - 2pm Fri ) show
9 108 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 1 ) show
6 100 moveto
( Wristbander - 8pm Fri ) show
9 92 moveto
( Fri 8:00 PM - 2hr - Reg Desk ) show
6 84 moveto
( Disability Assistance - 7pm Sat ) show
9 76 moveto
( Sat 7:00 PM - 2hr - ADA ) show
6 68 moveto
( Volunteer Lounge 1pm Sun ) show
9 60 moveto
( Sun 1:00 PM - 2hr - Boardroom ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Pete ) show
6 132 moveto
( Healthy, Wealthy, and Wise (moderating) ) show
9 124 moveto
( Sat 9:00 AM - 1hr 30min - 418 ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Phoenix ) show
6 132 moveto
( Load In: Team 1 - 10am Fri ) show
9 124 moveto
( Fri 10:00 AM - 2hr - Loading Dock - Team 1 ) show
6 116 moveto
( Load In: Team 1 - 12pm Fri ) show
9 108 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 1 ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( PhotoJoseph ) show
6 132 moveto
( Volunteer Lounge 5pm Sat ) show
9 124 moveto
( Sat 5:00 PM - 2hr - Boardroom ) show
6 116 moveto
( Wristbander - 9am Sun ) show
9 108 moveto
( Sun 9:00 AM - 2hr - Reg Desk ) show
6 100 moveto
( Doors and Floors - 2pm Sun ) show
9 92 moveto
( Sun 2:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( pinkmissive ) show
6 132 moveto
( Wristbander - 9am Sat ) show
9 124 moveto
( Sat 9:00 AM - 1hr - Reg Desk ) show
6 116 moveto
( Volunteer Lounge 11am Sat ) show
9 108 moveto
( Sat 11:00 AM - 2hr - Boardroom ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Pinupgirl1984  ) show
6 132 moveto
( Growing together through being apart (outside wristband checker) ) show
9 124 moveto
( Sat 10:45 AM - 1hr 30min - Naragansett B ) show
6 116 moveto
( Romantic Surrender (outside wristband checker) ) show
9 108 moveto
( Sat 2:45 PM - 1hr 30min - Naragansett C ) show
6 100 moveto
( Erotic Hypnosis 101 with DrSlashBlight (announcer/inside room attendant) ) show
9 92 moveto
( Sat 8:00 PM - 1hr 30min - Blackstone ) show
6 84 moveto
( Bootblacking 101 (announcer/inside room attendant) ) show
9 76 moveto
( Sun 11:45 AM - 1hr 30min - 418 ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Pisces Pagan ) show
6 132 moveto
( Doors and Floors - 1pm Sat ) show
9 124 moveto
( Sat 1:00 PM - 2hr - Doors-Floors ) show
6 116 moveto
( Doors and Floors - 5pm Sat ) show
9 108 moveto
( Sat 5:00 PM - 2hr - Doors-Floors ) show
6 100 moveto
( Doors and Floors - 10am Sun ) show
9 92 moveto
( Sun 10:00 AM - 2hr - Doors-Floors ) show
6 84 moveto
( Doors and Floors - 2pm Sun ) show
9 76 moveto
( Sun 2:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Princess Kali ) show
6 132 moveto
( Coming Out Kinky ) show
9 124 moveto
( Sat 2:45 PM - 1hr 30min - Rest. Annex ) show
6 116 moveto
( The Lazy Top's Guide to Pacing a Scene  (moderating) ) show
9 108 moveto
( Sun 1:45 PM - 1hr 30min - Naragansett A ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Puppy ) show
6 132 moveto
( Load In: Team 4 - 10am Fri ) show
9 124 moveto
( Fri 10:00 AM - 2hr - Loading Dock - Team 4 ) show
6 116 moveto
( Load In: Team 4 - 12pm Fri ) show
9 108 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 4 ) show
6 100 moveto
( Load In: Team 4 - 2pm Fri ) show
9 92 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 4 ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( puppy ) show
6 132 moveto
( NELA Associates Table - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - NELA Associates Sales ) show
6 116 moveto
( NELA Associates Table - 6pm Fri ) show
9 108 moveto
( Fri 6:00 PM - 2hr - NELA Associates Sales ) show
6 100 moveto
( NELA Associates Table - 12pm Sat ) show
9 92 moveto
( Sat 12:00 PM - 2hr - NELA Associates Sales ) show
6 84 moveto
( NELA Associates Table - 11am Sun ) show
9 76 moveto
( Sun 11:00 AM - 2hr - NELA Associates Sales ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( R100Ryder ) show
6 132 moveto
( Uniform fetish from the Mundane to the Military  (outside wristband checker) ) show
9 124 moveto
( Sat 9:00 AM - 1hr 30min - Naragansett C ) show
6 116 moveto
( Strong Slaves, Bodacious Bottoms (outside wristband checker) ) show
9 108 moveto
( Sat 8:00 PM - 1hr 30min - South County ) show
6 100 moveto
( Connection and Intent (outside wristband checker) ) show
9 92 moveto
( Sat 9:45 PM - 1hr 30min - South County ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Rabbit ) show
6 132 moveto
( Puppy Play with Rabbit (moderating) ) show
9 124 moveto
( Fri 6:15 PM - 1hr 30min - 518 ) show
6 116 moveto
( Bootblacking BOF (moderating) ) show
9 108 moveto
( Fri 10:00 PM - 1hr - Pres. Suite ) show
6 100 moveto
( Bootblacking Station Saturday (moderating) ) show
9 92 moveto
( Sat 9:00 AM - 12hr - Bootblacking ) show
6 84 moveto
( Bootblacking Station Sunday (moderating) ) show
9 76 moveto
( Sun 10:00 AM - 6hr - Bootblacking ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( RachaelBakes.com ) show
6 132 moveto
( Volunteer Lounge 11am Sat ) show
9 124 moveto
( Sat 11:00 AM - 2hr - Boardroom ) show
6 116 moveto
( Volunteer Lounge 1pm Sat ) show
9 108 moveto
( Sat 1:00 PM - 2hr - Boardroom ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Railen Panther ) show
6 132 moveto
( Volunteer Lounge 10am Fri ) show
9 124 moveto
( Fri 10:00 AM - 2hr - Boardroom ) show
6 116 moveto
( NELA Sales Table - 4pm Fri ) show
9 108 moveto
( Fri 4:00 PM - 2hr - NELA Sales ) show
6 100 moveto
( MAsT Meeting (moderating) ) show
9 92 moveto
( Fri 7:00 PM - 2hr - Pres. Suite ) show
6 84 moveto
( ID Checker - 11am Sun ) show
9 76 moveto
( Sun 11:00 AM - 2hr - Reg Desk-ID ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Raven Kaldera ) show
6 132 moveto
( Service & Devotion: Love in a Power Dynamic (moderating) ) show
9 124 moveto
( Fri 8:00 PM - 1hr 30min - 518 ) show
6 116 moveto
( Transgender Spirituality (moderating) ) show
9 108 moveto
( Fri 9:45 PM - 1hr 30min - 518 ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Rebecca ) show
6 132 moveto
( Load In: Team 2 - 6pm Fri ) show
9 124 moveto
( Fri 6:00 PM - 2hr - Loading Dock - Team 2 ) show
6 116 moveto
( ID Checker - 12pm Sat ) show
9 108 moveto
( Sat 12:00 PM - 2hr - Reg Desk-ID ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Reinette ) show
6 132 moveto
( Doors and Floors - 12pm Sun ) show
9 124 moveto
( Sun 12:00 PM - 2hr - Doors-Floors ) show
6 116 moveto
( Doors and Floors - 2pm Sun ) show
9 108 moveto
( Sun 2:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Remi ) show
6 132 moveto
( Volunteer Lounge 6pm Fri ) show
9 124 moveto
( Fri 6:00 PM - 2hr - Boardroom ) show
6 116 moveto
( Volunteer Lounge 1pm Sat ) show
9 108 moveto
( Sat 1:00 PM - 2hr - Boardroom ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Rev ) show
6 132 moveto
( Volunteer Lounge 6pm Fri ) show
9 124 moveto
( Fri 6:00 PM - 2hr - Boardroom ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( rhody ) show
6 132 moveto
( Load In: Team 2 - 10am Fri ) show
9 124 moveto
( Fri 10:00 AM - 2hr - Loading Dock - Team 2 ) show
6 116 moveto
( Load In: Team 2 - 12pm Fri ) show
9 108 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 2 ) show
6 100 moveto
( Load In: Team 2 - 2pm Fri ) show
9 92 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 2 ) show
6 84 moveto
( Load In: Team 2 - 4pm Fri ) show
9 76 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 2 ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Richard ) show
6 132 moveto
( Volunteer Lounge 11am Sat ) show
9 124 moveto
( Sat 11:00 AM - 2hr - Boardroom ) show
6 116 moveto
( Volunteer Lounge 1pm Sat ) show
9 108 moveto
( Sat 1:00 PM - 2hr - Boardroom ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( RiggerJay ) show
6 132 moveto
( Roper Room (moderating) ) show
9 124 moveto
( Sat 10:30 AM - 7hr 30min - Blackstone ) show
6 116 moveto
( Photography Exhibit (moderating) ) show
9 108 moveto
( Sun 10:00 AM - 6hr - Blackstone ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Rob ) show
6 132 moveto
( Load In: Team 5 - 2pm Fri ) show
9 124 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 5 ) show
6 116 moveto
( Load In: Team 5 - 4pm Fri ) show
9 108 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 5 ) show
6 100 moveto
( Load Out: Team 1 - 4pm Sun ) show
9 92 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 1 ) show
6 84 moveto
( Load Out: Team 1 - 6pm Sun ) show
9 76 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 1 ) show
6 68 moveto
( Load Out: Team 1 - 8pm Sun ) show
9 60 moveto
( Sun 8:00 PM - 2hr - Loading Dock - Team 1 ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Rob A ) show
6 132 moveto
( ID Checker - 9am Sat ) show
9 124 moveto
( Sat 9:00 AM - 1hr - Reg Desk-ID ) show
6 116 moveto
( ID Checker - 10am Sat ) show
9 108 moveto
( Sat 10:00 AM - 2hr - Reg Desk-ID ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Ronstone ) show
6 132 moveto
( Fuck Dogma (outside wristband checker) ) show
9 124 moveto
( Sat 1:00 PM - 1hr 30min - Naragansett C ) show
6 116 moveto
( Erotic Humiliation Play (announcer/inside room attendant) ) show
9 108 moveto
( Sat 2:45 PM - 1hr 30min - Naragansett A ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( RopeRider ) show
6 132 moveto
( Fucking With Your Camera (moderating) ) show
9 124 moveto
( Sat 4:30 PM - 1hr 30min - 418 ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Rosie ) show
6 132 moveto
( NELA Sales Table - 2pm Sat ) show
9 124 moveto
( Sat 2:00 PM - 2hr - NELA Sales ) show
6 116 moveto
( NELA Associates Table - 9am Sun ) show
9 108 moveto
( Sun 9:00 AM - 2hr - NELA Associates Sales ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Ryan ) show
6 132 moveto
( Load In: Team 6 - 10am Fri ) show
9 124 moveto
( Fri 10:00 AM - 2hr - Loading Dock - Team 6 ) show
6 116 moveto
( Load In: Team 6 - 12pm Fri ) show
9 108 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 6 ) show
6 100 moveto
( Volunteer Lounge 2pm Fri ) show
9 92 moveto
( Fri 2:00 PM - 2hr - Boardroom ) show
6 84 moveto
( Doors and Floors - 12pm Sun ) show
9 76 moveto
( Sun 12:00 PM - 2hr - Doors-Floors ) show
6 68 moveto
( Doors and Floors - 2pm Sun ) show
9 60 moveto
( Sun 2:00 PM - 2hr - Doors-Floors ) show
6 52 moveto
( Volunteer Lounge 5pm Sun ) show
9 44 moveto
( Sun 5:00 PM - 2hr - Boardroom ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Sabrina Sage ) show
6 132 moveto
( Disability Assistance - 10pm Fri ) show
9 124 moveto
( Fri 10:00 PM - 2hr - ADA ) show
6 116 moveto
( Doors and Floors - 11am Sat ) show
9 108 moveto
( Sat 11:00 AM - 2hr - Doors-Floors ) show
6 100 moveto
( Volunteer Lounge 5pm Sat ) show
9 92 moveto
( Sat 5:00 PM - 2hr - Boardroom ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Sanielle ) show
6 132 moveto
( Wristbander - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Reg Desk ) show
6 116 moveto
( Volunteer Lounge 8pm Fri ) show
9 108 moveto
( Fri 8:00 PM - 2hr - Boardroom ) show
6 100 moveto
( Volunteer Lounge 3pm Sat ) show
9 92 moveto
( Sat 3:00 PM - 2hr - Boardroom ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Sarah ) show
6 132 moveto
( Wristbander - 2pm Sat ) show
9 124 moveto
( Sat 2:00 PM - 2hr - Reg Desk ) show
6 116 moveto
( Doors and Floors - 2pm Sun ) show
9 108 moveto
( Sun 2:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Sarah (GRLee) ) show
6 132 moveto
( Doors and Floors - 11am Sat ) show
9 124 moveto
( Sat 11:00 AM - 2hr - Doors-Floors ) show
6 116 moveto
( Doors and Floors - 1pm Sat ) show
9 108 moveto
( Sat 1:00 PM - 2hr - Doors-Floors ) show
6 100 moveto
( Doors and Floors - 10am Sun ) show
9 92 moveto
( Sun 10:00 AM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( satifire ) show
6 132 moveto
( Disability Assistance - 8pm Fri ) show
9 124 moveto
( Fri 8:00 PM - 2hr - ADA ) show
6 116 moveto
( Disability Assistance - 5pm Sat ) show
9 108 moveto
( Sat 5:00 PM - 2hr - ADA ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Savannah Sly ) show
6 132 moveto
( Improvisational Skills for Role Play  (moderating) ) show
9 124 moveto
( Sun 10:00 AM - 1hr 30min - Rest. Annex ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Scott Erickson ) show
6 132 moveto
( Coming Out Kinky (moderating) ) show
9 124 moveto
( Sat 2:45 PM - 1hr 30min - Rest. Annex ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( sebrina ) show
6 132 moveto
( ID Checker - 6pm Fri ) show
9 124 moveto
( Fri 6:00 PM - 2hr - Reg Desk-ID ) show
6 116 moveto
( Doors and Floors - 1pm Sat ) show
9 108 moveto
( Sat 1:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( SensualRed ) show
6 132 moveto
( Scene Survival for New Players (outside wristband checker) ) show
9 124 moveto
( Fri 8:00 PM - 1hr 30min - Rest. Annex ) show
6 116 moveto
( Spirituality in BDSM (announcer/inside room attendant) ) show
9 108 moveto
( Sat 11:30 PM - 1hr 30min - South County ) show
6 100 moveto
( Laughing Our Way to Intimacy: Humor and Sex  (announcer/inside room attendant) ) show
9 92 moveto
( Sun 10:00 AM - 1hr 30min - Naragansett A ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Shelley ) show
6 132 moveto
( Doors and Floors - 3pm Sat ) show
9 124 moveto
( Sat 3:00 PM - 2hr - Doors-Floors ) show
6 116 moveto
( Volunteer Lounge 3pm Sat ) show
9 108 moveto
( Sat 3:00 PM - 2hr - Boardroom ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( sickcity ) show
6 132 moveto
( Load In: Team 1 - 10am Fri ) show
9 124 moveto
( Fri 10:00 AM - 2hr - Loading Dock - Team 1 ) show
6 116 moveto
( Load In: Team 1 - 12pm Fri ) show
9 108 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 1 ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Sid ) show
6 132 moveto
( Load In: Team 6 - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 6 ) show
6 116 moveto
( Doors and Floors - 2pm Sun ) show
9 108 moveto
( Sun 2:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Silverdreams ) show
6 132 moveto
( Traveling the Yellow Brick Road of Kink (moderating) ) show
9 124 moveto
( Sat 10:45 AM - 1hr 30min - 518 ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Simonn ) show
6 132 moveto
( Load In: Team 1 - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 1 ) show
6 116 moveto
( Load In: Team 1 - 6pm Fri ) show
9 108 moveto
( Fri 6:00 PM - 2hr - Loading Dock - Team 1 ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Sir Top Her ) show
6 132 moveto
( ID Checker - 6pm Fri ) show
9 124 moveto
( Fri 6:00 PM - 2hr - Reg Desk-ID ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( SleepyGreenEyes ) show
6 132 moveto
( Load In: Team 5 - 2pm Fri ) show
9 124 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 5 ) show
6 116 moveto
( ID Checker - 4pm Fri ) show
9 108 moveto
( Fri 4:00 PM - 2hr - Reg Desk-ID ) show
6 100 moveto
( Femdom Panel (announcer/inside room attendant) ) show
9 92 moveto
( Sat 10:45 AM - 1hr 30min - Pres. Suite ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Sol ) show
6 132 moveto
( Applied Rigging for Brain and Hands (moderating) ) show
9 124 moveto
( Fri 9:45 PM - 1hr 30min - South County ) show
6 116 moveto
( Dominant as Leader and Ultimate Servant (moderating) ) show
9 108 moveto
( Sat 2:45 PM - 1hr 30min - 418 ) show
6 100 moveto
( The Sensuous Art of Caning (moderating) ) show
9 92 moveto
( Sat 6:15 PM - 1hr 30min - South County ) show
6 84 moveto
( Setting the Scene and Building Trust (moderating) ) show
9 76 moveto
( Sun 11:45 AM - 1hr 30min - Naragansett A ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Solipsistic ) show
6 132 moveto
( TNG Meet & Greet ) show
9 124 moveto
( Sat 6:00 PM - 1hr - Pres. Suite ) show
6 116 moveto
( Age Play Panel ) show
9 108 moveto
( Sat 9:45 PM - 1hr 30min - 518 ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( SpaceExplorer ) show
6 132 moveto
( MonoPoly (outside wristband checker) ) show
9 124 moveto
( Fri 6:15 PM - 1hr 30min - South County ) show
6 116 moveto
( How to Throw a Whip (outside wristband checker) ) show
9 108 moveto
( Fri 8:00 PM - 1hr 30min - South County ) show
6 100 moveto
( The Call Beyond Equals (announcer/inside room attendant) ) show
9 92 moveto
( Sat 6:15 PM - 1hr 30min - Rest. Annex ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Sparkdog ) show
6 132 moveto
( So You Like it Rough? (outside wristband checker) ) show
9 124 moveto
( Sat 1:00 PM - 1hr 30min - Naragansett A ) show
6 116 moveto
( Erotic Humiliation Play (outside wristband checker) ) show
9 108 moveto
( Sat 2:45 PM - 1hr 30min - Naragansett A ) show
6 100 moveto
( Whose Kink is it Anyway? (outside wristband checker) ) show
9 92 moveto
( Sat 4:30 PM - 1hr 30min - Naragansett A ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Sparr ) show
6 132 moveto
( NELA Sales Table - 2pm Sat ) show
9 124 moveto
( Sat 2:00 PM - 2hr - NELA Sales ) show
6 116 moveto
( NELA Sales Table - 1pm Sun ) show
9 108 moveto
( Sun 1:00 PM - 2hr - NELA Sales ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Stargaze84 ) show
6 132 moveto
( Load In: Team 3 - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 3 ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Stella ) show
6 132 moveto
( Wristbander - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Reg Desk ) show
6 116 moveto
( Volunteer Lounge 9am Sun ) show
9 108 moveto
( Sun 9:00 AM - 2hr - Boardroom ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Stephen G ) show
6 132 moveto
( Wristbander - 9am Sat ) show
9 124 moveto
( Sat 9:00 AM - 1hr - Reg Desk ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Stephen Magnotta ) show
6 132 moveto
( Negotiating for Submissives (outside wristband checker) ) show
9 124 moveto
( Sat 4:30 PM - 1hr 30min - Naragansett C ) show
6 116 moveto
( Liminality of Rope (outside wristband checker) ) show
9 108 moveto
( Sat 6:15 PM - 1hr 30min - 418 ) show
6 100 moveto
( Pressure Points (outside wristband checker) ) show
9 92 moveto
( Sat 9:45 PM - 1hr 30min - 418 ) show
6 84 moveto
( How to Give/Go to a Play Party ... (outside wristband checker) ) show
9 76 moveto
( Sat 11:30 PM - 1hr 30min - 418 ) show
6 68 moveto
( Tickle Play with P.E.T.E (outside wristband checker) ) show
9 60 moveto
( Sun 1:45 PM - 1hr 30min - Naragansett B ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Stephen W ) show
6 132 moveto
( Load In: Team 3 - 10am Fri ) show
9 124 moveto
( Fri 10:00 AM - 2hr - Loading Dock - Team 3 ) show
6 116 moveto
( Load In: Team 3 - 12pm Fri ) show
9 108 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 3 ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Steve ) show
6 132 moveto
( Doors and Floors - 2pm Sun ) show
9 124 moveto
( Sun 2:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Steve B ) show
6 132 moveto
( Wristbander - 4pm Sat ) show
9 124 moveto
( Sat 4:00 PM - 2hr - Reg Desk ) show
6 116 moveto
( Doors and Floors - 12pm Sun ) show
9 108 moveto
( Sun 12:00 PM - 2hr - Doors-Floors ) show
6 100 moveto
( Doors and Floors - 2pm Sun ) show
9 92 moveto
( Sun 2:00 PM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Stormhawk ) show
6 132 moveto
( MonoPoly (announcer/inside room attendant) ) show
9 124 moveto
( Fri 6:15 PM - 1hr 30min - South County ) show
6 116 moveto
( TwoPlay: The Art of Making Out (announcer/inside room attendant) ) show
9 108 moveto
( Fri 8:00 PM - 1hr 30min - Blackstone ) show
6 100 moveto
( Tying Men (outside wristband checker) ) show
9 92 moveto
( Fri 9:45 PM - 1hr 30min - Rest. Annex ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( stormy ) show
6 132 moveto
( Wristbander - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Reg Desk ) show
6 116 moveto
( Doors and Floors - 11am Sat ) show
9 108 moveto
( Sat 11:00 AM - 2hr - Doors-Floors ) show
6 100 moveto
( Doors and Floors - 10am Sun ) show
9 92 moveto
( Sun 10:00 AM - 2hr - Doors-Floors ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Sub Jessie ) show
6 132 moveto
( Load In: Team 4 - 10am Fri ) show
9 124 moveto
( Fri 10:00 AM - 2hr - Loading Dock - Team 4 ) show
6 116 moveto
( Load In: Team 4 - 12pm Fri ) show
9 108 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 4 ) show
6 100 moveto
( Load In: Team 4 - 2pm Fri ) show
9 92 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 4 ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( SubStephen ) show
6 132 moveto
( Disability Assistance - 9am Sat ) show
9 124 moveto
( Sat 9:00 AM - 2hr - ADA ) show
6 116 moveto
( Doors and Floors - 5pm Sat ) show
9 108 moveto
( Sat 5:00 PM - 2hr - Doors-Floors ) show
6 100 moveto
( Wristbander - 1pm Sun ) show
9 92 moveto
( Sun 1:00 PM - 2hr - Reg Desk ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Sunshine ) show
6 132 moveto
( NELA Sales Table - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - NELA Sales ) show
6 116 moveto
( NELA Sales Table - 6pm Fri ) show
9 108 moveto
( Fri 6:00 PM - 2hr - NELA Sales ) show
6 100 moveto
( ID Checker - 10am Sat ) show
9 92 moveto
( Sat 10:00 AM - 2hr - Reg Desk-ID ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( SweetWisteria ) show
6 132 moveto
( Healthy, Wealthy, and Wise (announcer/inside room attendant) ) show
9 124 moveto
( Sat 9:00 AM - 1hr 30min - 418 ) show
6 116 moveto
( Playing with Saran Wrap (announcer/inside room attendant) ) show
9 108 moveto
( Sat 4:30 PM - 1hr 30min - Rest. Annex ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Tamara ) show
6 132 moveto
( Load In: Team 2 - 12pm Fri ) show
9 124 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 2 ) show
6 116 moveto
( Load In: Team 3 - 2pm Fri ) show
9 108 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 3 ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Tattooed Sailor ) show
6 132 moveto
( Load Out: Team 6 - 4pm Sun ) show
9 124 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 6 ) show
6 116 moveto
( Load Out: Team 6 - 6pm Sun ) show
9 108 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 6 ) show
6 100 moveto
( Load Out: Team 6 - 8pm Sun ) show
9 92 moveto
( Sun 8:00 PM - 2hr - Loading Dock - Team 6 ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( tedbii ) show
6 132 moveto
( Doors and Floors - 9am Sat ) show
9 124 moveto
( Sat 9:00 AM - 2hr - Doors-Floors ) show
6 116 moveto
( NELA Associates Table - 2pm Sat ) show
9 108 moveto
( Sat 2:00 PM - 2hr - NELA Associates Sales ) show
6 100 moveto
( NELA Associates Table - 4pm Sat ) show
9 92 moveto
( Sat 4:00 PM - 2hr - NELA Associates Sales ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( TheLauren ) show
6 132 moveto
( Mommy Dearest (moderating) ) show
9 124 moveto
( Sat 1:00 PM - 1hr 30min - 518 ) show
6 116 moveto
( Whose Kink is it Anyway? ) show
9 108 moveto
( Sat 4:30 PM - 1hr 30min - Naragansett A ) show
6 100 moveto
( You're doing it wrong (moderating) ) show
9 92 moveto
( Sun 1:45 PM - 1hr 30min - 418 ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( tiemeupleigh ) show
6 132 moveto
( Tie 'Em up and Fuck 'Em (outside wristband checker) ) show
9 124 moveto
( Sat 9:00 AM - 1hr 30min - Naragansett B ) show
6 116 moveto
( Strong Slaves, Bodacious Bottoms (announcer/inside room attendant) ) show
9 108 moveto
( Sat 8:00 PM - 1hr 30min - South County ) show
6 100 moveto
( Connection and Intent (announcer/inside room attendant) ) show
9 92 moveto
( Sat 9:45 PM - 1hr 30min - South County ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Tigglette  ) show
6 132 moveto
( Load In: Team 2 - 12pm Fri ) show
9 124 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 2 ) show
6 116 moveto
( Load In: Team 2 - 2pm Fri ) show
9 108 moveto
( Fri 2:00 PM - 2hr - Loading Dock - Team 2 ) show
6 100 moveto
( Load In: Team 2 - 4pm Fri ) show
9 92 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 2 ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Tiny_Terror ) show
6 132 moveto
( Load In: Team 5 - 10am Fri ) show
9 124 moveto
( Fri 10:00 AM - 2hr - Loading Dock - Team 5 ) show
6 116 moveto
( Load In: Team 5 - 12pm Fri ) show
9 108 moveto
( Fri 12:00 PM - 2hr - Loading Dock - Team 5 ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Tony P ) show
6 132 moveto
( Doors and Floors - 10am Sun ) show
9 124 moveto
( Sun 10:00 AM - 2hr - Doors-Floors ) show
6 116 moveto
( Wristbander - 11am Sun ) show
9 108 moveto
( Sun 11:00 AM - 2hr - Reg Desk ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Trialsinner ) show
6 132 moveto
( Tying Men (moderating) ) show
9 124 moveto
( Fri 9:45 PM - 1hr 30min - Rest. Annex ) show
6 116 moveto
( Liminality of Rope (moderating) ) show
9 108 moveto
( Sat 6:15 PM - 1hr 30min - 418 ) show
6 100 moveto
( Power and Authority Exchange Relationships ) show
9 92 moveto
( Sat 11:30 PM - 1hr 30min - 518 ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( TrojenHelen ) show
6 132 moveto
( Volunteer Lounge 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Boardroom ) show
6 116 moveto
( Load In: Team 5 - 6pm Fri ) show
9 108 moveto
( Fri 6:00 PM - 2hr - Loading Dock - Team 5 ) show
6 100 moveto
( For your own good (announcer/inside room attendant) ) show
9 92 moveto
( Sun 11:45 AM - 1hr 30min - Naragansett C ) show
6 84 moveto
( You're doing it wrong (announcer/inside room attendant) ) show
9 76 moveto
( Sun 1:45 PM - 1hr 30min - 418 ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( TSC ) show
6 132 moveto
( Load Out: Team 6 - 4pm Sun ) show
9 124 moveto
( Sun 4:00 PM - 2hr - Loading Dock - Team 6 ) show
6 116 moveto
( Load Out: Team 6 - 6pm Sun ) show
9 108 moveto
( Sun 6:00 PM - 2hr - Loading Dock - Team 6 ) show
6 100 moveto
( Load Out: Team 6 - 8pm Sun ) show
9 92 moveto
( Sun 8:00 PM - 2hr - Loading Dock - Team 6 ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Vilit ) show
6 132 moveto
( Volunteer Lounge 6pm Fri ) show
9 124 moveto
( Fri 6:00 PM - 2hr - Boardroom ) show
6 116 moveto
( Volunteer Lounge 8pm Fri ) show
9 108 moveto
( Fri 8:00 PM - 2hr - Boardroom ) show
6 100 moveto
( 4th Floor - 9am Sat ) show
9 92 moveto
( Sat 9:00 AM - 2hr - CM-4th Floor ) show
6 84 moveto
( 4th Floor - 11am Sat ) show
9 76 moveto
( Sat 11:00 AM - 2hr - CM-4th Floor ) show

stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( VioletDarling ) show
6 132 moveto
( Service with a Smile (announcer/inside room attendant) ) show
9 124 moveto
( Fri 6:15 PM - 1hr 30min - 418 ) show
6 116 moveto
( Universal and Sensual Foot Experience (announcer/inside room attendant) ) show
9 108 moveto
( Sat 9:00 AM - 1hr 30min - Naragansett A ) show
6 100 moveto
( Growing together through being apart (announcer/inside room attendant) ) show
9 92 moveto
( Sat 10:45 AM - 1hr 30min - Naragansett B ) show
6 84 moveto
( Improvisational Skills for Role Play  (announcer/inside room attendant) ) show
9 76 moveto
( Sun 10:00 AM - 1hr 30min - Rest. Annex ) show
6 68 moveto
( Getting Good Head (announcer/inside room attendant) ) show
9 60 moveto
( Sun 11:45 AM - 1hr 30min - 518 ) show

stroke
grestore

showpage

%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
252 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Vivienne ) show
6 132 moveto
( Alternative Activism ) show
9 124 moveto
( Sun 1:45 PM - 1hr 30min - 518 ) show

stroke
grestore

gsave
252 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Whosthatbear ) show
6 132 moveto
( Load In: Team 2 - 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Loading Dock - Team 2 ) show

stroke
grestore

gsave
252 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Wintersong ) show
6 132 moveto
( Dark Role Play ) show
9 124 moveto
( Sat 10:45 AM - 1hr 30min - Naragansett C ) show
6 116 moveto
( Spirituality in BDSM ) show
9 108 moveto
( Sat 11:30 PM - 1hr 30min - South County ) show
6 100 moveto
( Alternative Activism ) show
9 92 moveto
( Sun 1:45 PM - 1hr 30min - 518 ) show

stroke
grestore

gsave
252 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( YaniT  ) show
6 132 moveto
( Doors and Floors - 3pm Sat ) show
9 124 moveto
( Sat 3:00 PM - 2hr - Doors-Floors ) show
6 116 moveto
( Doors and Floors - 5pm Sat ) show
9 108 moveto
( Sat 5:00 PM - 2hr - Doors-Floors ) show
6 100 moveto
( Volunteer Lounge 11am Sun ) show
9 92 moveto
( Sun 11:00 AM - 2hr - Boardroom ) show

stroke
grestore

gsave
0 0
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( Zuma ) show
6 132 moveto
( Volunteer Lounge 4pm Fri ) show
9 124 moveto
( Fri 4:00 PM - 2hr - Boardroom ) show
6 116 moveto
( Narr C - 9am Sat ) show
9 108 moveto
( Sat 9:00 AM - 2hr - CM-Narr C ) show

stroke
grestore

gsave
0 160
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
(  ) show
6 132 moveto
(  ) show
9 124 moveto
(  ) show

stroke
grestore

gsave
0 320
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( ) show
stroke
grestore

gsave
0 480
translate
labelclip
newpath
ISOArial 6 scalefont setfont
6 140 moveto
( ) show
stroke
grestore

showpage

