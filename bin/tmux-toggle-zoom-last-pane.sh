#!/bin/bash

if tmux list-panes -F '#F' | grep -q Z; then
    tmux resize-pane -Z
    tmux select-pane -l
else
    tmux select-pane -l
    tmux resize-pane -Z
fi
