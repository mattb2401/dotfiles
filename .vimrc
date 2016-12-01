
execute pathogen#infect()

set encoding=utf-8

set nocompatible
let mapleader=","

if $COLORTERM == 'gnome-terminal'
  set t_Co=256
endif

let g:gruvbox_italic=0
set background=dark
colorscheme gruvbox

filetype on
filetype plugin on

if !exists("syntax_on")
    syntax on
endif
" Remove menubar
set guioptions-=m
set guioptions-=T
set guicursor+=a:blinkon0
set tabstop=4
set softtabstop=4
set shiftwidth=4
set expandtab
set autoindent
set smartindent
set number
set relativenumber
" Disable irritating sound
set vb
set pastetoggle=<F10>

set mouse=a
"if &term =~ '^screen'
"    " tmux knows the extended mouse mode
"    set ttymouse=xterm2
"endif
if has("mouse_sgr")
    set ttymouse=sgr
else
    set ttymouse=xterm2
end

hi Normal ctermbg=none
highlight NonText ctermbg=none

" Search and map handling
nnoremap <C-q> :noh<return><esc>
set ignorecase
set hlsearch

" delete without yanking
nnoremap <leader>d "_d
vnoremap <leader>d "_d

" replace currently selected text with default register
" without yanking it
vnoremap <leader>p "_dP

map vadl $v_o$hd"_dd<esc>
map vayl $v_o$hy<esc>
map val $v_o$h
map vak v0o$h
map vaj v$h
map vaf {jwv}k$h
map vadj v$hd<esc>
map vaop o<esc>Vp
map vaoo o<C-v><esc>
map vaip op

" Make vim handle windowing stuff
set guioptions-=a
set guifont=Monospace\ 9
set guitablabel=\[%N\]\ %t\ %M 

" Make word wrapping and selection work more sane
"behave mswin

" Multi cursor settings
let g:multi_cursor_exit_from_insert_mode=0

map <C-w>v :vsplit<cr><C-w>l
map <C-w>s :split<cr><C-w>j
map <C-w>\ <C-w><C-_><C-w><C-\|>

map <C-w><Up> :res +4<CR>
map <C-w><Down> :res -4<CR>
map <C-w><Left> :vertical resize -4<CR>
map <C-w><Right> :vertical resize +4<CR>

imap <C-v> <F10><C-r>"<F10>

"xclip stuff
vmap "+y :!xclip -f -selection clipboard<cr>u
map "+p :exec "set paste" \| exec "r!xclip -o -selection clipboard" \| exec "set nopaste"<cr>

" Tab Handling and navigation
nnoremap <silent> <C-W>t :tabnew<CR>
nnoremap <silent> <C-W>e :tabclose<CR>
nnoremap <silent> <C-W>u :tabNext<CR>

map <A-1> 1gt
map yy1 1gt
imap yy1 <Esc>1gt
vmap yy1 1gt
map <A-2> 2gt
map yy2 2gt
imap yy2 <Esc>2gt
vmap yy2 2gt
map <A-3> 3gt
map yy3 3gt
imap yy3 <Esc>3gt
vmap yy3 3gt
map <A-4> 4gt
map yy4 4gt
imap yy4 <Esc>4gt
vmap yy4 4gt
map <A-5> 5gt
map yy5 5gt
imap yy5 <Esc>5gt
vmap yy5 5gt
map <A-6> 6gt
map yy6 6gt
imap yy6 <Esc>6gt
vmap yy6 6gt
map <A-7> 7gt
map yy7 7gt
imap yy7 <Esc>7gt
vmap yy7 7gt
map <A-8> 8gt
map yy8 8gt
imap yy8 <Esc>8gt
vmap yy8 8gt
map <A-9> 9gt
map yy9 9gt
imap yy9 <Esc>9gt
vmap yy9 9gt

" Remap escape to something closer to core in insert mode
imap jj <Esc>

" Save mappings
map <C-s> :w<cr>
imap <C-s> <Esc>:w<cr>
map <C-S-s> :wa<cr>
imap <C-S-s> <Esc>:wa<cr>

let delimitMate_expand_cr = 1
filetype indent plugin on

function! Multiple_cursors_before()
    DelimitMateOff
    let delimitMate_expand_cr = 0
    filetype indent plugin off
    set noexpandtab
    set noautoindent
    set nosmartindent
endfunction

function! Multiple_cursors_after()
    DelimitMateOn
    let delimitMate_expand_cr = 1
    filetype indent plugin on
    set expandtab
    set autoindent
    set smartindent
endfunction

" Find mapping
nmap <space> /

" Make Ctrl+l, insert ; at end of line
nmap <C-l> :<C-u>call MyAppendToEnd(";")<CR>
imap <C-l> <Esc>:<C-u>call MyAppendToEnd(";")<CR>a
nmap <C-k> $a
imap <C-k> <Esc>$a
imap <C-f>0 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>o}
imap <C-f>1 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jg0>>o}
imap <C-f>2 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv1jg0><Esc>1jo}
imap <C-f>3 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv2jg0><Esc>2jo}
imap <C-f>4 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv3jg0><Esc>3jo}
imap <C-f>5 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv4jg0><Esc>4jo}
imap <C-f>6 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv5jg0><Esc>5jo}
imap <C-f>7 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv6jg0><Esc>6jo}
imap <C-f>8 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv7jg0><Esc>7jo}
imap <C-f>9 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv8jg0><Esc>8jo}

imap <C-f>10 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv9jg0><Esc>9jo}
imap <C-f>11 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv10jg0><Esc>10jo}
imap <C-f>12 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv11jg0><Esc>11jo}
imap <C-f>13 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv12jg0><Esc>12jo}
imap <C-f>14 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv13jg0><Esc>13jo}
imap <C-f>15 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv14jg0><Esc>14jo}
imap <C-f>16 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv15jg0><Esc>15jo}
imap <C-f>17 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv16jg0><Esc>16jo}
imap <C-f>18 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv17jg0><Esc>17jo}
imap <C-f>19 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv18jg0><Esc>18jo}

imap <C-f>20 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv19jg0><Esc>19jo}
imap <C-f>21 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv20jg0><Esc>20jo}
imap <C-f>22 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv21jg0><Esc>21jo}
imap <C-f>23 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv22jg0><Esc>22jo}
imap <C-f>24 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv23jg0><Esc>23jo}
imap <C-f>25 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv24jg0><Esc>24jo}
imap <C-f>26 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv25jg0><Esc>25jo}
imap <C-f>27 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv26jg0><Esc>26jo}
imap <C-f>28 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv27jg0><Esc>27jo}
imap <C-f>29 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv28jg0><Esc>28jo}

imap <C-f>30 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv29jg0><Esc>29jo}
imap <C-f>31 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv30jg0><Esc>30jo}
imap <C-f>32 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv31jg0><Esc>31jo}
imap <C-f>33 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv32jg0><Esc>32jo}
imap <C-f>34 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv33jg0><Esc>33jo}
imap <C-f>35 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv34jg0><Esc>34jo}
imap <C-f>36 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv35jg0><Esc>35jo}
imap <C-f>37 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv36jg0><Esc>36jo}
imap <C-f>38 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv37jg0><Esc>37jo}
imap <C-f>39 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv38jg0><Esc>38jo}

imap <C-f>40 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv39jg0><Esc>39jo}
imap <C-f>41 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv40jg0><Esc>40jo}
imap <C-f>42 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv41jg0><Esc>41jo}
imap <C-f>43 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv42jg0><Esc>42jo}
imap <C-f>44 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv43jg0><Esc>43jo}
imap <C-f>45 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv44jg0><Esc>44jo}
imap <C-f>46 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv45jg0><Esc>45jo}
imap <C-f>47 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv46jg0><Esc>46jo}
imap <C-f>48 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv47jg0><Esc>47jo}
imap <C-f>49 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv48jg0><Esc>48jo}

imap <C-f>50 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv49jg0><Esc>49jo}
imap <C-f>51 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv50jg0><Esc>50jo}
imap <C-f>52 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv51jg0><Esc>51jo}
imap <C-f>53 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv52jg0><Esc>52jo}
imap <C-f>54 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv53jg0><Esc>53jo}
imap <C-f>55 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv54jg0><Esc>54jo}
imap <C-f>56 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv55jg0><Esc>55jo}
imap <C-f>57 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv56jg0><Esc>56jo}
imap <C-f>58 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv57jg0><Esc>57jo}
imap <C-f>59 <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv58jg0><Esc>58jo}

imap <C-f>} <Esc>:<C-u>call MyAppendToEnd(" {")<CR>jv}g0><Esc>}o}

function! MyAppendToEnd(valueToAdd)
    let ln = line('.')
    let cl = col('.')
    let text = getline('.')
    call setline(ln,text . a:valueToAdd)
    call cursor(ln,cl)
endfunction

" Make alt key work in terminal
let c='a'
while c <= 'z'
  exec "set <A-".c.">=\e".c
  exec "imap \e".c." <A-".c.">"
  let c = nr2char(1+char2nr(c))
endw
set timeoutlen=1000 ttimeoutlen=0

vnoremap r "hy:%s/<C-r>h//gc<left><left><left>
vnoremap <C-r> h:s///g<left><left><left>

" backspace and cursor keys wrap to previous/next line
"set backspace=indent,eol,start whichwrap+=<,>,[,]

" backspace in Visual mode deletes selection
"vnoremap <BS> d

" Indent/Unindent
nmap <Tab> >>
nmap <S-Tab> a<C-d><Esc>
imap <S-Tab> <C-d>

vmap <silent> <Tab>        :<C-u>call My_indent("v")<CR>
vmap <silent> <S-Tab>      :<C-u>call My_unindent("v")<CR>

function! My_indent(mode)
    normal gv
    normal >
    normal gv
endfunction
 
function! My_unindent(mode)
    normal gv
    normal <
    normal gv
endfunction

set tabline=%!MyTabLine()  " custom tab pages line
function MyTabLine()
        let s = '' " complete tabline goes here
        " loop through each tab page
        for t in range(tabpagenr('$'))
                " set highlight
                if t + 1 == tabpagenr()
                        let s .= '%#TabLineSel#'
                else
                        let s .= '%#TabLine#'
                endif
                " set the tab page number (for mouse clicks)
                let s .= '%' . (t + 1) . 'T'
                let s .= ' '
                " set page number string
                let s .= t + 1 . ' '
                " get buffer names and statuses
                let n = ''      "temp string for buffer names while we loop and check buftype
                let m = 0       " &modified counter
                let bc = len(tabpagebuflist(t + 1))     "counter to avoid last ' '
                let tooLong = 0
                let maxLength = 20
                " loop through each buffer in a tab
                for b in tabpagebuflist(t + 1)
                    if tooLong == 0
                        if len(n) < maxLength
                            " buffer types: quickfix gets a [Q], help gets [H]{base fname}
                            " others get 1dir/2dir/3dir/fname shortened to 1/2/3/fname
                            if getbufvar( b, "&buftype" ) == 'help'
                                    let n .= '[H]' . fnamemodify( bufname(b), ':t:s/.txt$//' )
                            elseif getbufvar( b, "&buftype" ) == 'quickfix'
                                    let n .= '[Q]'
                            else
                                    "let n .= pathshorten(bufname(b))
                                    "let n .= bufname(b)
                                    let n .= fnamemodify(bufname(b), ':t')
                            endif
                            " check and ++ tab's &modified count
                            if getbufvar( b, "&modified" )
                                    let m += 1
                            endif
                            " no final ' ' added...formatting looks better done later
                            if bc > 1
                                if len(n) < maxLength
                                    let n .= ', '
                                endif
                            endif
                            let bc -= 1
                        else
                            let n .= ".."
                            let tooLong = 1
                        endif
                    endif
                endfor
                " add modified label [n+] where n pages in tab are modified
                if m > 0
                        let s .= '[' . m . '+]'
                endif
                " select the highlighting for the buffer names
                " my default highlighting only underlines the active tab
                " buffer names.
                if t + 1 == tabpagenr()
                        let s .= '%#TabLineSel#'
                else
                        let s .= '%#TabLine#'
                endif
                " add buffer names
                if n == ''
                        let s.= '[New]'
                else
                        let s .= n
                endif
                " switch to no underlining and add final space to buffer list
                let s .= ' '
        endfor
        " after the last tab fill with TabLineFill and reset tab page nr
        let s .= '%#TabLineFill#%T'
        " right-align the label to close the current tab page
        if tabpagenr('$') > 1
                let s .= '%=%#TabLineFill#%999Xclose'
        endif
        return s
endfunction

" OpenIDE commands
if has('win32')
        map <C-S-j> :let x = system('oi set-to-foreground ContinuousTests "ContinuousTests Standalone Client"')$
        imap <C-S-j> <Esc>:let x = system('oi set-to-foreground ContinuousTests "ContinuousTests Standalone Cli$

        map <C-S-y> :let x = system('oi gototype')<cr>
        imap <C-S-y> <Esc>:let x = system('oi gototype')<cr>

        map <C-S-e> :let x = system('oi explore')<cr>
        imap <C-S-e> <Esc>:let x = system('oi explore')<cr>
else
        " Open IDE shortcuts
        "map <C-S-y> :nbkey gototype<cr>
        "imap <C-S-y> <Esc>:nbkey gototype<cr>
		map <C-S-y> :let x = system("/home/ack/bin/OpenIDE/.OpenIDE/rscripts/tmux-editor-handler-files/typesearch-launcher")<cr>
		imap <C-S-y> <Esc> :let x = system("/home/ack/bin/OpenIDE/.OpenIDE/rscripts/tmux-editor-handler-files/typesearch-launcher")<cr>

        map <C-S-j> :nbkey autotest.net setfocus<cr>
        imap <C-S-j> <Esc>:nbkey autotest.net setfocus<cr>

        map <C-S-e> :nbkey explore<cr>
        imap <C-S-e> <Esc>:nbkey explore<cr>
endif

imap <F5> <Esc>:!oi x run<cr>
map <F5> :!oi x run<cr>
map <A-o> :let x = system("oi codemodel publish \"complete-snippet\"")<cr>
map yyo :let x = system("oi codemodel publish \"complete-snippet\"")<cr>
map <C-h> :let x = system("oi codemodel publish \"." . &ft . " command tamper-at-caret\"")<cr>
map <A-g> :let x = system("oi codemodel publish \"." . &ft . " command navigate-at-caret\"")<cr>
map yyg :let x = system("oi codemodel publish \"." . &ft . " command navigate-at-caret\"")<cr>
map <A-l> :let x = system("oi codemodel publish \"." . &ft . " command command-at-caret\"")<cr>
map yyl :let x = system("oi codemodel publish \"." . &ft . " command command-at-caret\"")<cr>
map yyd :let x = system("oi codemodel publish \"." . &ft . " command go-to-definition\"")<cr>
map yyvd <C-w>v:let x = system("oi codemodel publish \"." . &ft . " command go-to-definition\"")<cr>
map yysd <C-w>s:let x = system("oi codemodel publish \"." . &ft . " command go-to-definition\"")<cr>
map yyd :let x = system("oi codemodel publish \"." . &ft . " command go-to-definition\"")<cr>
map <A-S-y> :let x = system("oi codemodel publish \"gototype-extension\"")<cr>
map <A-r> :let x = system("oi codemodel publish \"evaluate-at-caret\"")<cr>
map yyr :let x = system("oi codemodel publish \"evaluate-at-caret\"")<cr>
map <A-n> :let x = system("oi codemodel publish \"." . &ft . " command new-from-caret\"")<cr>
map yyn :let x = system("oi codemodel publish \"." . &ft . " command new-from-caret\"")<cr>
map <A-e> :let x = system("oi codemodel publish \"." . &ft . " command evaluate-file\"")<cr>
map 0e :let x = system("oi codemodel publish \"." . &ft . " command evaluate-file\"")<cr>
"map <C-p> :let x = system("/home/ack/bin/OpenIDE/.OpenIDE/rscripts/tmux-editor-handler-files/file-search-launcher")<cr>
"imap <C-p> <Esc> :let x = system("/home/ack/bin/OpenIDE/.OpenIDE/rscripts/tmux-editor-handler-files/file-search-launcher")<cr>

"here is a more exotic version of my original Kwbd script
"delete the buffer; keep windows; create a scratch buffer if no buffers left
function s:Kwbd(kwbdStage)
  if(a:kwbdStage == 1)
    if(!buflisted(winbufnr(0)))
      bd!
      return
    endif
    let s:kwbdBufNum = bufnr("%")
    let s:kwbdWinNum = winnr()
    windo call s:Kwbd(2)
    execute s:kwbdWinNum . 'wincmd w'
    let s:buflistedLeft = 0
    let s:bufFinalJump = 0
    let l:nBufs = bufnr("$")
    let l:i = 1
    while(l:i <= l:nBufs)
      if(l:i != s:kwbdBufNum)
        if(buflisted(l:i))
          let s:buflistedLeft = s:buflistedLeft + 1
        else
          if(bufexists(l:i) && !strlen(bufname(l:i)) && !s:bufFinalJump)
            let s:bufFinalJump = l:i
          endif
        endif
      endif
      let l:i = l:i + 1
    endwhile
    if(!s:buflistedLeft)
      if(s:bufFinalJump)
        windo if(buflisted(winbufnr(0))) | execute "b! " . s:bufFinalJump | endif
      else
        enew
        let l:newBuf = bufnr("%")
        windo if(buflisted(winbufnr(0))) | execute "b! " . l:newBuf | endif
      endif
      execute s:kwbdWinNum . 'wincmd w'
    endif
    if(buflisted(s:kwbdBufNum) || s:kwbdBufNum == bufnr("%"))
      execute "bd! " . s:kwbdBufNum
    endif
    if(!s:buflistedLeft)
      set buflisted
      set bufhidden=delete
      set buftype=
      setlocal noswapfile
    endif
  else
    if(bufnr("%") == s:kwbdBufNum)
      let prevbufvar = bufnr("#")
      if(prevbufvar > 0 && buflisted(prevbufvar) && prevbufvar != s:kwbdBufNum)
        b #
      else
        bn
      endif
    endif
  endif
endfunction

command! Kwbd call s:Kwbd(1)
nnoremap <silent> <Plug>Kwbd :<C-u>Kwbd<CR>

" Delete buffer shortcut
nnoremap <silent> <C-w>d :close<CR>
nnoremap <silent> <C-w>d :bdelete<CR>
nnoremap <silent> <C-w>p :Kwbd<CR>

map <C-k><C-b> :NERDTreeToggle<CR>
map <C-k><C-h> :NERDTreeFocus<CR>
map <C-k><C-f> :NERDTreeFind<CR>
let g:NERDTreeWinSize=50

" enable line numbers
let NERDTreeShowLineNumbers=1
" make sure relative line numbers are used
autocmd FileType nerdtree setlocal relativenumber

map <C-x><C-x> <esc>:qa<cr>
map <C-x><C-x> :qa<cr> 

set laststatus=2

map <Leader> <Plug>(easymotion-prefix)

map  / <Plug>(easymotion-sn)
omap / <Plug>(easymotion-tn)

let g:fsharp_xbuild_path = "/urs/bin/xbuild"
let g:fsharp_completion_helptext=1
let g:fsharp_only_check_errors_on_write=1

