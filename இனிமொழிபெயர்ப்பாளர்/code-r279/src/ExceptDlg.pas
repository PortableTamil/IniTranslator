{**************************************************************************************************}
{                                                                                                  }
{ Project JEDI Code Library (JCL)                                                                  }
{                                                                                                  }
{ The contents of this file are subject to the Mozilla Public License Version 1.1 (the "License"); }
{ you may not use this file except in compliance with the License. You may obtain a copy of the    }
{ License at http://www.mozilla.org/MPL/                                                           }
{                                                                                                  }
{ Software distributed under the License is distributed on an "AS IS" basis, WITHOUT WARRANTY OF   }
{ ANY KIND, either express or implied. See the License for the specific language governing rights  }
{ and limitations under the License.                                                               }
{                                                                                                  }
{ The Original Code is ExceptDlg.pas.                                                              }
{                                                                                                  }
{ The Initial Developer of the Original Code is documented in the accompanying                     }
{ help file JCL.chm. Portions created by these individuals are Copyright (C) of these individuals. }
{                                                                                                  }
{**************************************************************************************************}
{                                                                                                  }
{ Sample Application exception dialog replacement                                                  }
{                                                                                                  }
{ Last modified: June 5, 2002                                                                      }
{                                                                                                  }
{**************************************************************************************************}
// $Id: ExceptDlg.pas 249 2007-08-14 16:29:55Z peter3 $
unit ExceptDlg;

{$I jcl.inc}

interface

uses
  Windows, Messages, SysUtils, Classes, Graphics, Controls, Forms, Dialogs,
  StdCtrls, ExtCtrls, JclDebug, Menus, ComCtrls,
  BaseForm,
  TntWindows, TntClasses, TntSysUtils, TntComCtrls, TntStdCtrls, TntExtCtrls;

const
  UM_CREATEDETAILS = WM_USER + $100;

  ReportToLogEnabled = $00000001; // TExceptionDialog.Tag property
  DisableTextScrollbar = $00000002; // TExceptionDialog.Tag property

type
  TSimpleExceptionLog = class(TObject)
  private
    FLogFileHandle:THandle;
    FLogFileName:WideString;
    FLogWasEmpty:Boolean;
    function GetLogOpen:Boolean;
  protected
    function CreateDefaultFileName:WideString;
  public
    constructor Create(const ALogFileName:WideString = '');
    destructor Destroy; override;
    procedure CloseLog;
    procedure OpenLog;
    procedure Write(const Text:WideString; Indent:Integer = 0); overload;
    procedure Write(Strings:TTntStrings; Indent:Integer = 0); overload;
    procedure WriteStamp(SeparatorLen:Integer = 0);
    property LogFileName:WideString read FLogFileName;
    property LogOpen:Boolean read GetLogOpen;
  end;

  TExcDialogSystemInfo = (siStackList, siOsInfo, siModuleList, siActiveControls);
  TExcDialogSystemInfos = set of TExcDialogSystemInfo;

  TExceptionDialog = class(Tfrmbase)
    OkBtn:TTntButton;
    DetailsMemo:TTntRichEdit;
    DetailsBtn:TTntButton;
    Bevel1:TBevel;
    pnlTop:TTntPanel;
    Label1:TTntLabel;
    Label2:TTntLabel;
    TextLabel:TTntRichEdit;
    Label3:TTntLabel;
    Bevel2:TBevel;
    procedure FormPaint(Sender:TObject);
    procedure FormCreate(Sender:TObject);
    procedure FormShow(Sender:TObject);
    procedure DetailsBtnClick(Sender:TObject);
    procedure FormKeyDown(Sender:TObject; var Key:Word; Shift:TShiftState);
    procedure FormDestroy(Sender:TObject);
    procedure FormResize(Sender:TObject);
  private
    FDetailsVisible:Boolean;
    FIsMainThead:Boolean;
    FLastActiveControl:TWinControl;
    FNonDetailsHeight:Integer;
    FFullHeight:Integer;
    FSimpleLog:TSimpleExceptionLog;
    procedure CreateDetails;
    function GetReportAsText:WideString;
    procedure ReportToLog;
    procedure SetDetailsVisible(const Value:Boolean);
    procedure UMCreateDetails(var Message:TMessage); message UM_CREATEDETAILS;
  protected
    procedure AfterCreateDetails; dynamic;
    procedure BeforeCreateDetails; dynamic;
    procedure CreateDetailInfo; dynamic;
    procedure CreateReport(const SystemInfo:TExcDialogSystemInfos);
    function ReportMaxColumns:Integer; virtual;
    function ReportNewBlockDelimiterChar:Char; virtual;
    procedure NextDetailBlock;
    procedure UpdateTextLabelScrollbars;
  public
    procedure CopyReportToClipboard;
    class procedure ExceptionHandler(Sender:TObject; E:Exception);
    class procedure ExceptionThreadHandler(Thread:TJclDebugThread);
    class procedure ShowException(E:Exception; Thread:TJclDebugThread);
    property DetailsVisible:Boolean read FDetailsVisible write SetDetailsVisible;
    property ReportAsText:WideString read GetReportAsText;
    property SimpleLog:TSimpleExceptionLog read FSimpleLog;
  end;

  TExceptionDialogClass = class of TExceptionDialog;

var
  ExceptionDialogClass:TExceptionDialogClass = TExceptionDialog;

implementation

{$R *.DFM}

uses
  TntClipBrd, Math,
  JclBase, JclFileUtils, JclHookExcept, JclPeImage, JclStrings, JclSysInfo, JclSysUtils,
  TntSystem;

resourcestring
  RsAppError = '%s - application error';
  RsExceptionClass = 'Exception class: %s';
  RsExceptionAddr = 'Exception address: %p';
  RsStackList = 'Stack list, generated %s';
  RsModulesList = 'List of loaded modules:';
  RsOSVersion = 'System   : %s %s, Version: %d.%d, Build: %x, "%s"';
  RsProcessor = 'Processor: %s, %s, %d MHz %s%s';
  RsScreenRes = 'Display  : %dx%d pixels, %d bpp';
  RsActiveControl = 'Active Controls hiearchy:';
  RsThread = 'Thread: %s';
  RsMissingVersionInfo = '(no version info)';

var
  ExceptionDialog:TExceptionDialog;

//==================================================================================================
// Helper routines
//==================================================================================================

function GetBPP:Integer;
var
  DC:HDC;
begin
  DC := GetDC(0);
  Result := GetDeviceCaps(DC, BITSPIXEL) * GetDeviceCaps(DC, PLANES);
  ReleaseDC(0, DC);
end;

//--------------------------------------------------------------------------------------------------

function SortModulesListByAddressCompare(List:TTntStringList; Index1, Index2:Integer):Integer;
begin
  Result := Integer(List.Objects[Index1]) - Integer(List.Objects[Index2]);
end;

//==================================================================================================
// TApplication.HandleException method code hooking for exceptions from DLLs
//==================================================================================================

// We need to catch the last line of TApplication.HandleException method:
// [...]
//   end else
//    SysUtils.ShowException(ExceptObject, ExceptAddr);
// end;

procedure HookShowException(ExceptObject:TObject; ExceptAddr:Pointer);
begin
  if JclValidateModuleAddress(ExceptAddr) and (ExceptObject.InstanceSize >= Exception.InstanceSize) then
    TExceptionDialog.ExceptionHandler(nil, Exception(ExceptObject))
  else
    SysUtils.ShowException(ExceptObject, ExceptAddr);
end;

//--------------------------------------------------------------------------------------------------

function HookTApplicationHandleException:Boolean;
const
  CallOffset = $86;
  CallOffsetDebug = $94;
type
  PCALLInstruction = ^TCALLInstruction;
  TCALLInstruction = packed record
    Call:Byte;
    Address:Integer;
  end;
var
  TApplicationHandleExceptionAddr, SysUtilsShowExceptionAddr:Pointer;
  CALLInstruction:TCALLInstruction;
  CallAddress:Pointer;
  NW:DWORD;

  function CheckAddressForOffset(Offset:Cardinal):Boolean;
  begin
    try
      CallAddress := Pointer(Cardinal(TApplicationHandleExceptionAddr) + Offset);
      CALLInstruction.Call := $E8;
      Result := PCALLInstruction(CallAddress)^.Call = CALLInstruction.Call;
      if Result then
      begin
        if IsCompiledWithPackages then
          Result := PeMapImgResolvePackageThunk(Pointer(Integer(CallAddress) + Integer(PCALLInstruction(CallAddress)^.Address) + SizeOf(CALLInstruction))) = SysUtilsShowExceptionAddr
        else
          Result := PCALLInstruction(CallAddress)^.Address = Integer(SysUtilsShowExceptionAddr) - Integer(CallAddress) - SizeOf(CALLInstruction);
      end;
    except
      Result := False;
    end;
  end;

begin
  TApplicationHandleExceptionAddr := PeMapImgResolvePackageThunk(@TApplication.HandleException);
  SysUtilsShowExceptionAddr := PeMapImgResolvePackageThunk(@SysUtils.ShowException);
  Result := CheckAddressForOffset(CallOffset) or CheckAddressForOffset(CallOffsetDebug);
  if Result then
  begin
    CALLInstruction.Address := Integer(@HookShowException) - Integer(CallAddress) - SizeOf(CALLInstruction);
    Result := WriteProcessMemory(GetCurrentProcess, CallAddress, @CALLInstruction, SizeOf(CALLInstruction), NW);
    if Result then
      FlushInstructionCache(GetCurrentProcess, CallAddress, SizeOf(CALLInstruction));
  end;
end;

//==================================================================================================
// TSimpleExceptionLog
//==================================================================================================

procedure TSimpleExceptionLog.CloseLog;
begin
  if LogOpen then
  begin
    CloseHandle(FLogFileHandle);
    FLogFileHandle := INVALID_HANDLE_VALUE;
    FLogWasEmpty := False;
  end;
end;

//--------------------------------------------------------------------------------------------------

constructor TSimpleExceptionLog.Create(const ALogFileName:WideString);
begin
  if ALogFileName = '' then
    FLogFileName := CreateDefaultFileName
  else
    FLogFileName := ALogFileName;
  FLogFileHandle := INVALID_HANDLE_VALUE;
end;

//--------------------------------------------------------------------------------------------------

function TSimpleExceptionLog.CreateDefaultFileName:WideString;
begin

  Result := WideIncludeTrailingPathDelimiter(WideExtractFileDir(WideParamStr(0))) +
    WideChangeFileExt(WideExtractFileName(WideParamStr(0)), '') + WideString('_Err.log');
//  Result := PathExtractFileDirFixed(ParamStr(0)) + PathExtractFileNameNoExt(ParamStr(0)) + '_Err.log';
end;

//--------------------------------------------------------------------------------------------------

destructor TSimpleExceptionLog.Destroy;
begin
  CloseLog;
  inherited;
end;

//--------------------------------------------------------------------------------------------------

function TSimpleExceptionLog.GetLogOpen:Boolean;
begin
  Result := FLogFileHandle <> INVALID_HANDLE_VALUE;
end;

//--------------------------------------------------------------------------------------------------

procedure TSimpleExceptionLog.OpenLog;
begin
  if not LogOpen then
  begin
    FLogFileHandle := Tnt_CreateFileW(PWideChar(FLogFileName), GENERIC_WRITE, FILE_SHARE_READ, nil,
      OPEN_ALWAYS, FILE_ATTRIBUTE_NORMAL, 0);
    if LogOpen then
      FLogWasEmpty := SetFilePointer(FLogFileHandle, 0, nil, FILE_END) = 0;
  end
  else
    FLogWasEmpty := False;
end;

//--------------------------------------------------------------------------------------------------

procedure TSimpleExceptionLog.Write(const Text:WideString; Indent:Integer);
var
  S:WideString;
  SL:TTntStringList;
  I:Integer;
begin
  if LogOpen then
  begin
    SL := TTntStringList.Create;
    try
      SL.Text := Text;
      for I := 0 to SL.Count - 1 do
      begin
        S := StringOfChar(WideChar(' '), Indent) + StrEnsureSuffix(AnsiCrLf, TrimRight(SL[I]));
        FileWrite(Integer(FLogFileHandle), Pointer(S)^, Length(S));
      end;
    finally
      SL.Free;
    end;
  end;
end;

//--------------------------------------------------------------------------------------------------

procedure TSimpleExceptionLog.Write(Strings:TTntStrings; Indent:Integer);
var
  I:Integer;
begin
  for I := 0 to Strings.Count - 1 do
    Write(Strings[I], Indent);
end;

//--------------------------------------------------------------------------------------------------

procedure TSimpleExceptionLog.WriteStamp(SeparatorLen:Integer);
begin
  if SeparatorLen = 0 then
    SeparatorLen := 100;
  SeparatorLen := Max(SeparatorLen, 20);
  OpenLog;
  if not FLogWasEmpty then
    Write(AnsiCrLf);
  Write(StrRepeat('=', SeparatorLen));
  Write(Format('= %-*s =', [SeparatorLen - 4, DateTimeToStr(Now)]));
  Write(StrRepeat('=', SeparatorLen));
end;

//==================================================================================================
// Exception dialog
//==================================================================================================

var
  ExceptionShowing:Boolean;

{ TExceptionDialog }

procedure TExceptionDialog.AfterCreateDetails;
begin
end;

//--------------------------------------------------------------------------------------------------

procedure TExceptionDialog.BeforeCreateDetails;
begin
end;

//--------------------------------------------------------------------------------------------------

procedure TExceptionDialog.CopyReportToClipboard;
begin
  TntClipBoard.AsWideText := ReportAsText;
end;

//--------------------------------------------------------------------------------------------------

procedure TExceptionDialog.CreateDetailInfo;
begin
  CreateReport([siStackList, siOsInfo, siModuleList, siActiveControls]);
end;

//--------------------------------------------------------------------------------------------------

procedure TExceptionDialog.CreateDetails;
begin
  Screen.Cursor := crHourGlass;
  DetailsMemo.Lines.BeginUpdate;
  try
    CreateDetailInfo;
    ReportToLog;
    DetailsMemo.SelStart := 0;
    SendMessage(DetailsMemo.Handle, EM_SCROLLCARET, 0, 0);
    AfterCreateDetails;
  finally
    DetailsMemo.Lines.EndUpdate;
    OkBtn.Enabled := True;
    DetailsBtn.Enabled := True;
    OkBtn.SetFocus;
    Screen.Cursor := crDefault;
  end;
end;

//--------------------------------------------------------------------------------------------------

procedure TExceptionDialog.CreateReport(const SystemInfo:TExcDialogSystemInfos);
const
  MMXText:array[Boolean] of PWideChar = ('', 'MMX');
  FDIVText:array[Boolean] of PWideChar = (' [FDIV Bug]', '');
var
  SL:TTntStringList;
  I:Integer;
  ModuleName:TFileName;
  CpuInfo:TCpuInfo;
  C:TWinControl;
  NtHeaders:PImageNtHeaders;
  ModuleBase:Cardinal;
  ImageBaseStr:WideString;
  StackList:TJclStackInfoList;
begin
  SL := TTntStringList.Create;
  try
    // Stack list
    if siStackList in SystemInfo then
    begin
      StackList := JclLastExceptStackList;
      if Assigned(StackList) then
      begin
        DetailsMemo.Lines.Add(Format(RsStackList, [DateTimeToStr(StackList.TimeStamp)]));
        StackList.AddToStrings(DetailsMemo.Lines.AnsiStrings, False, True, True);
        NextDetailBlock;
      end;
    end;
    // System and OS information
    if siOsInfo in SystemInfo then
    begin
      DetailsMemo.Lines.Add(Format(RsOSVersion, [GetWindowsVersionString, NtProductTypeString,
        Win32MajorVersion, Win32MinorVersion, Win32BuildNumber, Win32CSDVersion]));
      GetCpuInfo(CpuInfo);
      with CpuInfo do
        DetailsMemo.Lines.Add(Format(RsProcessor, [Manufacturer, CpuName,
          RoundFrequency(FrequencyInfo.NormFreq),
            MMXText[MMX], FDIVText[IsFDIVOK]]));
      DetailsMemo.Lines.Add(Format(RsScreenRes, [Screen.Width, Screen.Height, GetBPP]));
      NextDetailBlock;
    end;
    // Modules list
    if (siModuleList in SystemInfo) and LoadedModulesList(SL.AnsiStrings, GetCurrentProcessId) then
    begin
      DetailsMemo.Lines.Add(RsModulesList);
{$IFDEF DELPHI4}
      StringListCustomSort(SL, SortModulesListByAddressCompare);
{$ELSE DELPHI4}
      SL.CustomSort(SortModulesListByAddressCompare);
{$ENDIF DELPHI4}
      for I := 0 to SL.Count - 1 do
      begin
        ModuleName := SL[I];
        ModuleBase := Cardinal(SL.Objects[I]);
        DetailsMemo.Lines.Add(Format('[%.8x] %s', [ModuleBase, ModuleName]));
        NtHeaders := PeMapImgNtHeaders(Pointer(ModuleBase));
        if (NtHeaders <> nil) and (NtHeaders^.OptionalHeader.ImageBase <> ModuleBase) then
          ImageBaseStr := Format('<%.8x> ', [NtHeaders^.OptionalHeader.ImageBase])
        else
          ImageBaseStr := StrRepeat(' ', 11);
        if VersionResourceAvailable(ModuleName) then
          with TJclFileVersionInfo.Create(ModuleName) do
          try
            DetailsMemo.Lines.Add(ImageBaseStr + BinFileVersion + ' - ' + FileVersion);
            if FileDescription <> '' then
              DetailsMemo.Lines.Add(StrRepeat(' ', 11) + FileDescription);
          finally
            Free;
          end
        else
          DetailsMemo.Lines.Add(ImageBaseStr + RsMissingVersionInfo);
      end;
      NextDetailBlock;
    end;
    // Active controls
    if (siActiveControls in SystemInfo) and (FLastActiveControl <> nil) then
    begin
      DetailsMemo.Lines.Add(RsActiveControl);
      C := FLastActiveControl;
      while C <> nil do
      begin
        DetailsMemo.Lines.Add(Format('%s "%s"', [C.ClassName, C.Name]));
        C := C.Parent;
      end;
      NextDetailBlock;
    end;
  finally
    SL.Free;
  end;
end;

//--------------------------------------------------------------------------------------------------

procedure TExceptionDialog.DetailsBtnClick(Sender:TObject);
begin
  DetailsVisible := not DetailsVisible;
end;

//--------------------------------------------------------------------------------------------------

class procedure TExceptionDialog.ExceptionHandler(Sender:TObject; E:Exception);
begin
  if ExceptionShowing then
    Application.ShowException(E)
  else
  begin
    ExceptionShowing := True;
    try
      ShowException(E, nil);
    finally
      ExceptionShowing := False;
    end;
  end;
end;

//--------------------------------------------------------------------------------------------------

class procedure TExceptionDialog.ExceptionThreadHandler(Thread:TJclDebugThread);
begin
  if ExceptionShowing then
    Application.ShowException(Thread.SyncException as Exception)
  else
  begin
    ExceptionShowing := True;
    try
      ShowException(Thread.SyncException as Exception, Thread);
    finally
      ExceptionShowing := False;
    end;
  end;
end;

//--------------------------------------------------------------------------------------------------

procedure TExceptionDialog.FormCreate(Sender:TObject);
begin
  FSimpleLog := TSimpleExceptionLog.Create;
  FFullHeight := ClientHeight;
  DetailsVisible := False;
  Caption := Format(RsAppError, [Application.Title]);
end;

//--------------------------------------------------------------------------------------------------

procedure TExceptionDialog.FormDestroy(Sender:TObject);
begin
  FreeAndNil(FSimpleLog);
end;

//--------------------------------------------------------------------------------------------------

procedure TExceptionDialog.FormKeyDown(Sender:TObject; var Key:Word; Shift:TShiftState);
begin
  if (Key = Ord('C')) and (ssCtrl in Shift) then
  begin
    CopyReportToClipboard;
    MessageBeep(MB_OK);
  end;
end;

//--------------------------------------------------------------------------------------------------

procedure TExceptionDialog.FormPaint(Sender:TObject);
begin
  DrawIcon(Canvas.Handle, TextLabel.Left - GetSystemMetrics(SM_CXICON) - 15,
    TextLabel.Top, LoadIcon(0, IDI_ERROR));
end;

//--------------------------------------------------------------------------------------------------

procedure TExceptionDialog.FormResize(Sender:TObject);
begin
  UpdateTextLabelScrollbars;
end;

//--------------------------------------------------------------------------------------------------

procedure TExceptionDialog.FormShow(Sender:TObject);
begin
  BeforeCreateDetails;
  MessageBeep(MB_ICONERROR);
  if FIsMainThead and (GetWindowThreadProcessId(Handle, nil) = MainThreadID) then
    PostMessage(Handle, UM_CREATEDETAILS, 0, 0)
  else
    CreateDetails;
end;

//--------------------------------------------------------------------------------------------------

function StrEnsureSuffix(const Suffix, Text:WideString):WideString;
var
  SuffixLen:Integer;
begin
  SuffixLen := Length(Suffix);
  if Copy(Text, Length(Text) - SuffixLen + 1, SuffixLen) = Suffix then
    Result := Text
  else
    Result := Text + Suffix;
end;

function TExceptionDialog.GetReportAsText:WideString;
begin
  Result := StrEnsureSuffix(sLineBreak, TextLabel.Text) + sLineBreak + DetailsMemo.Text;
end;

//--------------------------------------------------------------------------------------------------

procedure TExceptionDialog.NextDetailBlock;
begin
  DetailsMemo.Lines.Add(StrRepeat(ReportNewBlockDelimiterChar, ReportMaxColumns));
end;

//--------------------------------------------------------------------------------------------------

function TExceptionDialog.ReportMaxColumns:Integer;
begin
  Result := 100;
end;

//--------------------------------------------------------------------------------------------------

function TExceptionDialog.ReportNewBlockDelimiterChar:Char;
begin
  Result := '-';
end;

//--------------------------------------------------------------------------------------------------

procedure TExceptionDialog.ReportToLog;
begin
  if Tag and ReportToLogEnabled <> 0 then
  begin
    FSimpleLog.WriteStamp(ReportMaxColumns);
    try
      FSimpleLog.Write(ReportAsText);
    finally
      FSimpleLog.CloseLog;
    end;
  end;
end;

//--------------------------------------------------------------------------------------------------

procedure TExceptionDialog.SetDetailsVisible(const Value:Boolean);
var
  DetailsCaption:WideString;
begin
  FDetailsVisible := Value;
  DetailsCaption := Trim(StrRemoveChars(DetailsBtn.Caption, ['<', '>']));
  if Value then
  begin
    Constraints.MinHeight := FNonDetailsHeight + 100;
    Constraints.MaxHeight := Screen.Height;
    DetailsCaption := '<< ' + DetailsCaption;
    ClientHeight := FFullHeight;
    DetailsMemo.Height := FFullHeight - DetailsMemo.Top - 3;
  end
  else
  begin
    FFullHeight := ClientHeight;
    DetailsCaption := DetailsCaption + ' >>';
    if FNonDetailsHeight = 0 then
    begin
      ClientHeight := Bevel1.Top;
      FNonDetailsHeight := Height;
    end
    else
      Height := FNonDetailsHeight;
    Constraints.MinHeight := FNonDetailsHeight;
    Constraints.MaxHeight := FNonDetailsHeight
  end;
  DetailsBtn.Caption := DetailsCaption;
  DetailsMemo.Enabled := Value;
end;

//--------------------------------------------------------------------------------------------------

class procedure TExceptionDialog.ShowException(E:Exception; Thread:TJclDebugThread);
begin
  if ExceptionDialog = nil then
    ExceptionDialog := ExceptionDialogClass.Create(Application);
  try
    with ExceptionDialog do
    begin
      FIsMainThead := (GetCurrentThreadId = MainThreadID);
      FLastActiveControl := Screen.ActiveControl;
      TextLabel.Text := AdjustLineBreaks(StrEnsureSuffix('.', E.Message));
      UpdateTextLabelScrollbars;
      DetailsMemo.Lines.Add(Format(RsExceptionClass, [E.ClassName]));
      if Thread = nil then
        DetailsMemo.Lines.Add(Format(RsExceptionAddr, [ExceptAddr]))
      else
        DetailsMemo.Lines.Add(Format(RsThread, [Thread.ThreadInfo]));
      NextDetailBlock;
      ShowModal;
    end;
  finally
    FreeAndNil(ExceptionDialog);
  end;
end;

//--------------------------------------------------------------------------------------------------

procedure TExceptionDialog.UMCreateDetails(var Message:TMessage);
begin
  Update;
  CreateDetails;
end;

//--------------------------------------------------------------------------------------------------

procedure TExceptionDialog.UpdateTextLabelScrollbars;
begin
{  if Tag and DisableTextScrollbar = 0 then
  begin
    Canvas.Font := TextLabel.Font;
    if TextLabel.Lines.Count * Canvas.TextHeight('Wg') > TextLabel.ClientHeight then
      TextLabel.ScrollBars := ssVertical
    else
      TextLabel.ScrollBars := ssNone;
   end; }
end;

//==================================================================================================
// Exception handler initialization code
//==================================================================================================

procedure InitializeHandler;
begin
  JclStackTrackingOptions := JclStackTrackingOptions + [stRawMode];
{$IFNDEF HOOK_DLL_EXCEPTIONS}
  JclStackTrackingOptions := JclStackTrackingOptions + [stStaticModuleList];
{$ENDIF HOOK_DLL_EXCEPTIONS}
  JclDebugThreadList.OnSyncException := TExceptionDialog.ExceptionThreadHandler;
  JclStartExceptionTracking;
{$IFDEF HOOK_DLL_EXCEPTIONS}
  if HookTApplicationHandleException then
    JclTrackExceptionsFromLibraries;
{$ENDIF HOOK_DLL_EXCEPTIONS}
  Application.OnException := TExceptionDialog.ExceptionHandler;
end;

//--------------------------------------------------------------------------------------------------

procedure UnInitializeHandler;
begin
  Application.OnException := nil;
  JclDebugThreadList.OnSyncException := nil;
  JclUnhookExceptions;
  JclStopExceptionTracking;
end;

//--------------------------------------------------------------------------------------------------

initialization
  InitializeHandler;

finalization
  UnInitializeHandler;

end.
