# dotfiles
All the configuratoin, binaries and dot files for a "clean" system

## Tmux
Start tmux either by runnin tmux. Use tmux attach to start tmux and attach to a already running session. Or use wdtmux which starts or attaches to a tmux session named by convention using current directory.

| Keyboard shortcut                         | Description                                     |
|:------------------------------------------|:------------------------------------------------|
| Ctrl+A, "                                 | Split pane horizontally                         |
| Ctrl+A, %                                 | Split pane vertically                           |
| Ctrl+A, r                                 | Split pane horizontally small                   |
| Ctrl+A, t                                 | Split pane vertically small                     |
| Ctrl+A, q                                 | Close pane                                      |
| Ctrl+A, h                                 | Select left pane                                |
| Ctrl+A, j                                 | Select pane below                               |
| Ctrl+A, k                                 | Select pane above                               |
| Ctrl+A, l                                 | Select right pane                               |
| Ctrl+A, b                                 | Toggle zoom on other pane                       |
| Ctrl+A, c                                 | Create new window (tab)                         |
| Ctrl+A, [0-9]                             | Select window                                   |
| Ctrl+A, o                                 | Toggle last window                              |
| Ctrl+A, n                                 | Select next window                              |
| Ctrl+A, Esc                               | Enter select mode                               |
| ---> vim keys, navigation keys            | To navigate                                     |
| ---> v                                    | Start text selection                            |
| ---> y                                    | Yank the text to tmux buffer                    |
| Ctrl+A, p                                 | Past text from tmux buffer                      |
| Ctrl+A, y                                 | Copy text from tmux buffer to xclipboard        |
| Ctrl+A, z                                 | Toggle zoom for active pane                     |
| Ctrl+A, w                                 | Choose between open windows                     |
| Ctrl+A, s                                 | Choose between running tmux sessions            |
| Ctrl+A, d                                 | Detach from a running tmux session              |

## Vim
Use oivim to start OpenIDE running vim in a terminal.

| Keyboard shortcut                         | Description                                     |
|:------------------------------------------|:------------------------------------------------|
| Navigation                                |                                                 |
| h/j/k/l                                   | Navigate left/down/up/right                     |
| w                                         | Navigate to begining of next word               |
| e                                         | Navigate to end of next word                    |
| b                                         | Navigate to beginning of previous word          |
| ge                                        | Navigate to end of previous word                |
| $                                         | Navigate to end of line                         |
| Ctrl+k                                    | Navigate to end of line and enter inster mode   |
| Ctrl+d                                    | Navigate half a page down                       |
| Ctrl+u                                    | Navigate half a page up                         |
| }                                         | Navigate down to next empty line                |
| {                                         | Navigate up to next empty line                  |
| _                                         | Navigate to first character in line             |
| g0                                        | Navigate to column 0 of line                    |
| %                                         | Navigate to matching (/[/{/..                   |
| [NUM]k                                    | Move [NUM] lines up                             |
| [NUM]j                                    | Move [NUM] lines down                           |
| [NUM]h                                    | Move [NUM] columns left                         |
| [NUM]l                                    | Move [NUM] columns right                        |
| gg                                        | Move to top                                     |
| G                                         | Move to bottom                                  |
| ''                                        | Move to last cursor position                    |
| Ctrl+k, Ctrl+b                            | Toggle NERDTree                                 |
| Ctrl+k, Ctrl+f                            | Open current file in NERDTree                   |
| Ctrl+w, s                                 | Split buffer horizontally                       |
| Ctrl+w, v                                 | Split buffer vertically                         |
| Ctrl+w, h/j/k/l                           | Navigate between buffers in a window            |
| Ctrl+w, t                                 | Open new tab                                    |
| Ctrl+w, T                                 | Open current file in new tab                    |
| Ctrl+w, c                                 | Close buffer                                    |
| Ctrl+w, e                                 | Close tab                                       |
| Ctrl+w, u                                 | Next tab                                        |
| Ctrl+w, /                                 | Zoom in to current buffer                       |
| Ctrl+w, =                                 | Resize buffers in window                        |
| Ctrl+w, [ARROW]                           | Resize buffer in direction                      |
| Alt, [NUM]                                | Open tab [NUM] (may not work in terminal)       |
| yy[NUM]                                   | Open tab [NUM]                                  |
| /                                         | Search                                          |
| Shift+[SPACE]                             | Search                                          |
| *                                         | Search forward for word under cursor            |
| #                                         | Search backward for word under cursor           |
| n                                         | Move to next hit in search                      |
| Shift+n                                   | Move to previous hit in search                  |
| Ctrl+q                                    | Clear search                                    |
|                                           |                                                 |
| Selection                                 |                                                 |
| yy                                        | Yank current line                               |
| yiw                                       | Yank inner word                                 |
| diw                                       | Delete inner word                               |
| viw                                       | Select inner word                               |
| y[MOTION]                                 | Yank using motion - yw (yank word)              |
| d[MOTION]                                 | Delete using motion - dw (delete word)          |
| v[MOTION]                                 | Select using motion - vw (select word)          |
| y[NUM][MOTION]                            | Yank motion repeat - y4w (yank 4 words)         |
| d[NUM][MOTION]                            | Delete motion repeat - d4w (delete 4 words)     |
| v[NUM][MOTION]                            | Select motion repeat - v4w (select 4 words)     |
| Ctrl+v                                    | Start block select                              |
| vap                                       | Select between empty lines                      |
| vaj                                       | Select to last character in line                |
| vak                                       | Select from first to last position in line      |
| val                                       | Select from first to last character in line     |
| viw                                       | Select inner word                               |
| vi[CHAR]                                  | Select inside (/)/[/]/{/}/...                   |
| va[CHAR]                                  | Select outside (/)/[/]/{/}/...                  |
|                                           |                                                 |
| When text is selected                     |                                                 |
| y                                         | Yank text                                       |
| c                                         | Delete, yank and enter insert mode              |
| d                                         | Delete selection and yank                       |
| ,d                                        | Delete selection without yank                   |
| o                                         | Move to end of selected text                    |
| O                                         | Move to begining of selected text               |
| "+y                                       | Yank to X-clipboard                             |
| "+v                                       | Paste from X-clipboard                          |
| Ctrl+n                                    | Multicursor selection                           |
|                                           |                                                 |
| Modification                              |                                                 |
| dd                                        | Delete line and yank line                       |
| diw                                       | Delete line and yank line                       |
| d{                                        | Delete to previous backspace                    |
| d                                         | Delete to next backspace                        |
| d$                                        | Delete to end of line and yank                  |
| ,dd                                       | Delete line without yank                        |
| ,d$                                       | Delete to end of line without yank              |
| p                                         | Past previously ynaked content                  |
| u                                         | Undo                                            |
| Ctrl+r                                    | Redo                                            |
| r[CHAR]                                   | Add replace current character with [CHAR]       |
| Ctrl+k                                    | Add semicolon to end of line                    |
| Ctrl+n                                    | Multicursor selection using word under cursor   |
| Ctrl+f,[NUM]                              | Wrap in {} from line end, indent NUM lines down |
| F10                                       | Toggle paste mode                               |
|                                           |                                                 |
| In insert mode                            |                                                 |
| jj                                        | Escape                                          |
|                                           |                                                 |
| Multicursor                               |                                                 |
| Ctrl+n                                    | Multicursor selection using word under cursor   |
| Ctrl+n                                    | If already in multicursor select next           |
| Ctrl+x                                    | If already in multicursor skip current          |
| Ctrl+p                                    | If already in multicursor jump one back         |
|                                           |                                                 |
| OpenIDE                                   |                                                 |
| F5                                        | Trigger oi run in terminal view                 |
| Alt+o / yyo                               | Complete snippet                                |
| Ctrl+h                                    | Run tamper at caret                             |
| Alt+g / yyg                               | Run navigate at caret                           |
| Alt+l / yyl                               | Run command at caret                            |
| Alt+d / yyd                               | Go to definition                                |
| Ctrl+Shift+y                              | Type search                                     |
| Ctrl+Shift+j                              | Focus ContinuousTests                           |
| Alt+Shift+y                               | Go to type by extension                         |
| Alt+r / yyr                               | Run evaluate at caret                           |
| Alt+e / yye                               | Run evaluate file at caret                      |
| Alt+n / yyn                               | New at caret                                    |
| Alt+f / yyf                               | Find interactive                                |
| Alt+f, a / yyfa                           | Find all interactive                            |
| Alt+f, w / yyfw                           | Find word at caret                              |
| Alt+f, y / yyfy                           | Find yml interactive                            |
| Alt+w, e                                  | Evaluate and append selection in repl           |
| Alt+w, r                                  | Run selection in repl                           |
| Alt+w, q                                  | Clear repl                                      |
|                                           |                                                 |
| Other                                     |                                                 |
| Ctrl+s                                    | Save                                            |
| Ctrl+Shift+s                              | Save all                                        |
| Ctrl+x, Ctrl+x                            | Exit vim                                        |

