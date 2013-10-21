%!PS-Adobe-3.0

/insertlogo (NELA-LOGO.eps) def
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
/ISOArial-Bold ISO-8859-1Encoding /Arial-Bold reencode_font
/labelclip {
	newpath
	1.000000 1.000000 moveto
	251.000000 1.000000 lineto
	251.000000 159.000000 lineto
	1.000000 159.000000 lineto
	closepath
	clip

} def

/BeginEPSF { % def
  /b4_Inc_state save def		%Save state for cleanup
  /dict_count countdictstack def	%Count dict objects on dict stack
  /op_count count 1 sub def		%Count objects on operand stack
  userdict begin			%Push userdict on dict stack
  /showpage { } def			%Redefine showpage null
  0 setgray 0 setlinecap 1 setlinewidth	%Graphics setup
  0 setlinejoin 10 setmiterlimit [] 0 setdash newpath
  /languagelevel where			%if level != 1 then set strokeadjust
  {pop languagelevel			%and overprint to their defaults
  1 ne
    {false setstrokeadjust false setoverprint
    } if
  } if
} bind def

/EndEPSF { % def
  count op_count sub {pop} repeat	%Clean up stacks
  countdictstack dict_count sub {end} repeat
  b4_Inc_state restore
  picwidth 4 div neg -5 translate                       % Attempt at centering
} bind def
/rect { % llx lly w h			Lower Left X&Y Width and Height inputs
  4 2 roll moveto			% mv llx and lly to top and go there 
  1 index 0 rlineto			% gets and copies width, lineto w,0
  0 exch rlineto			% switches 0 for hight, lineto 0,h
  neg 0 rlineto				% negs width, lineto -w,0
  closepath				% back to llx and lly
} bind def
/picinsert { % llx lly urx ury from BoundingBox
  /bi_ury exch def
  /bi_urx exch def
  /bi_lly exch def
  /bi_llx exch def
  /bi_width bi_urx bi_llx sub def
  /bi_height bi_ury bi_lly sub def
  /picwidth 246 def
  /picheight 20 def
  /scale_width picwidth bi_width div def
  /scale_height picheight bi_height div def
  picwidth 4 div 5 translate                       % Attempt at centering
  BeginEPSF
  scale_height scale_height scale                        %figured from BoundingBox
  bi_llx neg bi_lly neg translate       %-llx -lly to lower corner justify
  bi_llx bi_lly 
  picwidth scale_width div 
  picheight scale_height div rect               %playspace
  clip newpath
} bind def

% end prologue

% set font type and size
ISOArial 16 scalefont setfont
%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Abby) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( AbbyF) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Abelee) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Addie) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Alec) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Alex M) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Alexis Mae) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Amanda) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Ambra Storm) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Andrew) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Andrew M) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Angel) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Ann) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Ariel) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Ashley) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( AspiringWordsmith) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Athair) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( AthenaKali) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Autumn) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Ayem_Willing) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Bendyogagirl ) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( BIG EYES) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Bil) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Bluette) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Blur) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Bob) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Brandon) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( breeanne) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Buddy J) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Calexir) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Cameryn Moore) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Carnivorous) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Carolyn) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Casey) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Chunsa) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Cirra) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( coedwrestler35) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Coral) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Cory) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Courtney Jane) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Cwellan) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Dakota) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Dan) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Dana) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Danielle dv8) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Darksideblues ) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( David Wraith) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( De Lano) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Dean) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Deanna) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Deb) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Debbie) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Dee) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( demonic angel) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Deyan) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Dm Eric S) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Doc) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Domino) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Don) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Donna) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Dorje) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Dov) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Dr. Robert J. Rubel) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Dr. SlashBlight) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Dragon) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Dragonfly) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( DragonScott) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Drew) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( DrIrv) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Dsire) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Duncan) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Dunter ) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Dyanne) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Ed Drohan) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Edd) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Elisabetta) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( elizabeth) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Elizabeth DuPre) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Ella) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( ElleTrouble) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Eloff) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Emily) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Eponine) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Erika) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Evey) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Fallen's Own) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Fawnn) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Ferrous Caput) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Flirtrageous) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Frank) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( FuzzyJim) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( FyreWalkyr) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( girlMouse) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Goddess Diane) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Grace) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Graydancer) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Heather) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Herbie) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Hermes) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Hobbes_Kitten) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( hockeysub) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Honore) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( imasupermuteant) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( J3remoo) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Jack Frost) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Jack Steel) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Jade) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Jade Kitten) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Jakan) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( James) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Jawn's Doll) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Jax ) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Jaye ) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Jean) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Jeannine) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Jeff) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Jen) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( jenphalian) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Jera) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Jesse) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( joemash) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( John) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Jonah) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Jordan Mulder-Crouser) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Justin) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Kaimi) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Kalika) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Kat) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Kate) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Kathryn) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( katie) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Katy) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( katzelmacher) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( KeerBeau) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Kikea) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Kim N) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( KissableDom) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Kristen) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Kryssi Bee) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Kyle BostonDSM) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( La Dresseuse) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( La Louve) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Lady G) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Lady Shimla) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Lady Z) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Laura Antoniou) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Leather By Danny) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Lee Harrington) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Legionnaire06 ) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Lex) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( lilone27) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Lisa) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Lochai) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Locke) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( LoMevina) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Lori) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Louise) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( LqqkOut) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Lynn) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( M & M Peanut) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( M & M plain) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Mad Patter) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Madam Tsigane) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Madelyn (Miss Wyld)) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( MadLady) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Marceline) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Maria) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Marie) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Mark) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Marko) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Mary) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( MattzBattz) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( MBob) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Mel) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( melissa) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( mely) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Mephki) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Michael S) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Michele) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Midori) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Misfit Mike) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Mishi) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Miss Aurora) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Miss Cindy) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Miss Luna) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Mistress Thorne) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Mistress Tink) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( MistressTasteful) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Mizzy) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Mollena) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Molly) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Monique) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( moonlight) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Mr. M) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Mr. Powertie) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Ms. Leading) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Mud) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Murphy Blue) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Mystress Autumn) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Natella) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( nathaniel) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( NaughtyBaby) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( NaughtyEm) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( NaughtyRalphie) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( NELA Volunteer) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( NeoClassic) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( NH Kinkster Couple) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( NH Rope Slut) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Nik) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Nutmeg) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Nymeria) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Opn) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Oro) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( P.E.T.E.!) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Padhana) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( PandaPet) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Parks) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Paul A) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Paul C) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Peaches) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Pencildragon) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( pet tigress) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Pete) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Phoenix) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( PhotoJoseph) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( pinkmissive) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Pinupgirl1984 ) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Pisces Pagan) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Princess Kali) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( puppy) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Puppy) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( R100Ryder) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Rabbit) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( RachaelBakes.com) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Railen Panther) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Raven Kaldera) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Rebecca) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Reinette) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Remi) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Rev) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( rhody) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Richard) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( RiggerJay) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Rob) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Rob A) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Ronstone) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( RopeRider) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Rosie) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Ryan) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Sabrina Sage) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Sanielle) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Sarah) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Sarah (GRLee)) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( satifire) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Savannah Sly) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Scott Erickson) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( sebrina) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( SensualRed) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Shelley) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( sickcity) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Sid) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Silverdreams) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Simonn) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Sir Top Her) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( SleepyGreenEyes) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Sol) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Solipsistic) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( SpaceExplorer) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Sparkdog) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Sparr) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Stargaze84) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Stella) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Stephen G) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Stephen Magnotta) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Stephen W) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Steve) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Steve B) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Stormhawk) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( stormy) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Sub Jessie) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( SubStephen) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Sunshine) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( SweetWisteria) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Tamara) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Tattooed Sailor) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( tedbii) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( TheLauren) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( tiemeupleigh) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Tigglette ) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Tiny_Terror) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Tony P) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Trialsinner) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( TrojenHelen) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( TSC) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Vilit) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( VioletDarling) show
stroke
grestore

showpage

%%Page: BadgeFront

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup
gsave
0 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Vivienne) show
stroke
grestore

gsave
0 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Whosthatbear) show
stroke
grestore

gsave
0 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Presenter) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Wintersong) show
stroke
grestore

gsave
0 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( YaniT ) show
stroke
grestore

gsave
252 0
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( Volunteer) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( Zuma) show
stroke
grestore

gsave
252 160
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( ) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( ) show
stroke
grestore

gsave
252 320
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( ) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( ) show
stroke
grestore

gsave
252 480
translate
3 3 translate
0 0 759 222 picinsert
gsave
insertlogo run
grestore
%%Trailer
EndEPSF
-3 -3 translate
labelclip
newpath
ISOArial 16 scalefont setfont
3.000000 60.000000 moveto
( ) show
ISOArial 16 scalefont setfont
3.000000 40.000000 moveto
( FFF #38) show
ISOArial-Bold 24 scalefont setfont
3.000000 80.000000 moveto
( ) show
stroke
grestore

showpage

