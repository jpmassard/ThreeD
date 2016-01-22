HISTORY:

Piwigo 2.8RC1
"functions_upload.inc.php" needs some adjustments. Lines 223 to 239 must be removed 
manually, but the trigger_change mechanism is already in place

Piwigo 2.7:
This version is a complete refactoring for the 3D plugin.
Now, html5 upload is used for 3d files. 3D photo, video and batch menus
no longer exist.
You must replace the file "functions_upload.inc.php" in the piwigo directory
"admin/include" in order to operate correctly. The replacement file is at the root
of the plugin directory and have the same name. In this beta version,
file handlers for tif, pdf and various video format are rewritten, but NOT installed...

This version is for tests only. Use it at your own risk...

JP MASSARD
                                            
 