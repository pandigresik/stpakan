#include <GUIConstantsEx.au3>
#include <WindowsConstants.au3>

$ffapp="S T R U K • T I M B A N G - Mozilla Firefox"
$myapp="Timbangan"
GUICreate("Timbangan",300,90)
GUICtrlCreatePic('logowjc32.bmp',0,0,32,32)
$input=GUICtrlCreateInput('',70,33,80)
GUICtrlSetState($input,$GUI_DISABLE)
$kirim=GUICtrlCreateButton("Kirim", 155,30,70)
GUISetState()

While 1
	$msg = GUIGetMsg()

	If $msg = $kirim Then
		MsgBox(0,'','Hai..')
		kirimData()
	ElseIf $msg = $GUI_EVENT_CLOSE Then
		ExitLoop
	EndIf
WEnd


Func kirimData()
   $ffhandle=WinActivate($ffapp)
   If $ffhandle Then
	  Send('Hai ...')
   Else
	  MsgBox(0,'Error','Program timbangan tidak aktif')
   EndIf

EndFunc
