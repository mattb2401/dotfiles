lvim.plugins = {
  { 'jose-elias-alvarez/typescript.nvim' },

  { 'lunarvim/lunar.nvim' },
  { "morhetz/gruvbox" },
  { "haishanh/night-owl.vim" },
  { "rebelot/kanagawa.nvim" },
  { "tiagovla/tokyodark.nvim" },
  { "NLKNguyen/papercolor-theme" },

  { "sainnhe/gruvbox-material" },
  {
    "sainnhe/sonokai",
    priority = 1000,
    config = function()
      vim.g.sonokai_transparent_background = "1"
      vim.g.sonokai_enable_italic = "1"
      vim.g.sonokai_style = "andromeda"
      vim.cmd.colorscheme("sonokai")
    end,
  },
  { "sainnhe/edge" },
  { "lunarvim/horizon.nvim" },
  { "tomasr/molokai" },
  { "ayu-theme/ayu-vim" },
  { 'stevedylandev/flexoki-nvim', name = 'flexoki' },
  { "catppuccin/nvim",            name = "catppuccin", priority = 1000 },
  {
    "folke/trouble.nvim",
    cmd = "TroubleToggle",
  },
  {
    'akinsho/flutter-tools.nvim',
    lazy = false,
    dependencies = {
      'nvim-lua/plenary.nvim',
      'stevearc/dressing.nvim', -- optional for vim.ui.select
    },
  },
  {
    "craftzdog/solarized-osaka.nvim",
    lazy = false,
    priority = 1000,
    opts = {},
  },
  { 'rose-pine/neovim',           name = 'rose-pine' },
  {
    "zbirenbaum/copilot.lua",
    cmd = "Copilot",
    build = ":Copilot auth",
    opts = {
      suggestion = {
        enabled = false,
        auto_trigger = false,
        debounce = 75,
        keymap = {
          accept = "<M-l>",
          accept_word = false,
          accept_line = false,
          next = "<M-]>",
          prev = "<M-[>",
          dismiss = "<C-]>",
        },
      },
      panel = {
        enabled = false,
        auto_refresh = false,
        keymap = {
          jump_prev = "[[",
          jump_next = "]]",
          accept = "<CR>",
          refresh = "gr",
          open = "<M-CR>"
        },
        layout = {
          position = "bottom", -- | top | left | right
          ratio = 0.4
        },
      },
      filetypes = {
        markdown = true,
        help = true,
      },
    },
  },
  { "bluz72/vim-nightfly-colors", name = "nightfly", lazy = false, priority = 1000 },
  { 'projekt0n/github-nvim-theme' },
  {
    "folke/todo-comments.nvim",
    event = "BufRead",
    config = function()
      require("todo-comments").setup()
    end
  },

  {
    "folke/persistence.nvim",
    event = "BufReadPre",
    config = function()
      require("persistence").setup({
        dir = vim.fn.expand(vim.fn.stdpath "state" .. "/sessions/"),
        options = { "buffers", "curdir", "tabpages", "winsize" }
      })
    end
  },

  { "christoomey/vim-tmux-navigator" },
  { "tpope/vim-surround" },
  { "felipec/vim-sanegx",            event = "BufRead" },
  {
    "windwp/nvim-ts-autotag",
    config = function()
      require("nvim-ts-autotag").setup()
    end,
  },
  { "tpope/vim-repeat" },

  { "ThePrimeagen/harpoon" },

  {
    "kristijanhusak/vim-dadbod-ui",
    dependencies = {
      { 'tpope/vim-dadbod',                     lazy = true },
      { 'kristijanhusak/vim-dadbod-completion', ft = { 'sql', 'mysql', 'plsql' }, lazy = true },
    },
    cmd = {
      'DBUI',
      'DBUIToggle',
      'DBUIAddConnection',
      'DBUIFindBuffer',
    },
    init = function()
      -- Your DBUI configuration
      vim.g.db_ui_use_nerd_fonts = 1
    end,
    keys = {
      {
        "<leader>d",
        "<cmd>tabnew<cr><bar><bar><cmd>DBUI<cr>",
      }
    }
  },


  {
    'phaazon/hop.nvim',
    branch = 'v2',
    config = function()
      require('hop').setup()
    end
  },

  {
    'nvim-telescope/telescope-frecency.nvim',
    dependencies = { 'nvim-telescope/telescope.nvim', 'kkharji/sqlite.lua' },
  },

  {
    'AckslD/nvim-trevJ.lua',
    config = 'require("trevj").setup()',
    init = function()
      vim.keymap.set('n', '<leader>j', function()
        require('trevj').format_at_cursor()
      end)
    end,
  },

}

table.insert(lvim.plugins, {
  "zbirenbaum/copilot-cmp",
  event = "InsertEnter",
  dependencies = { "zbirenbaum/copilot.lua" },
  config = function()
    local ok, cmp = pcall(require, "copilot_cmp")
    if ok then cmp.setup({}) end
  end,
})

lvim.builtin.telescope.on_config_done = function(telescope)
  pcall(telescope.load_extension, "frecency")
end
local themes = {
  gruvbox = "gruvbox",
  rose_pine = "rose-pine",
  lunar = "lunar",
  gruvbox_material = "gruvbox-material",
  onedarker = "onedarker",
  onedark = "onedark",
  horizon = "horizon",
  tokyonight = "tokyonight",
  tokyonight_night = "tokyonight-night",
  tokyonight_day = "tokyonight-day",
  tokyonight_moon = "tokyonight-moon",
  desert = "desert",
  morning = "morning",
  sonokai = "sonokai",
  edge = "edge",
  ayu = "ayu",
  flexoki = "flexoki",
  catppuccin = "catppuccin",
  catppuccin_latte = "catppuccin-latte",
  catppuccin_mocha = "catppuccin-mocha",
  solarized_osaka = "solarized-osaka",
  nightfly = "nightfly",
  github_dark_colorblind = "github_dark_colorblind",
  github_dark = "github_dark",
  owl_dark = "night-owl",
  kanagawa = "kanagawa",
  tokyodark = "tokyodark",
  papercolor = "papercolor-theme"
}

lvim.transparent_window = true
lvim.colorscheme = themes.catppuccin_mocha

local extra_opts = {
  tokyodark = {
    transparent_background = false,                                        -- set background to transparent
    gamma = 1.00,                                                          -- adjust the brightness of the theme
    styles = {
      comments = { italic = true },                                        -- style for comments
      keywords = { italic = true },                                        -- style for keywords
      identifiers = { italic = true },                                     -- style for identifiers
      functions = {},                                                      -- style for functions
      variables = {},                                                      -- style for variables
    },
    custom_highlights = {} or function(highlights, palette) return {} end, -- extend highlights
    custom_palette = {} or function(palette) return {} end,                -- extend palette
    terminal_colors = true,                                                -- enable terminal colors
  },
  sonokai = {
    styles = {
      espresso = "espresso",
      shusia = "shusia",
      default = "default",
    },
  },
  edge = {
    styles = {
      aura = "aura",
      neon = "neon",
    },
    airline_theme = "edge",
    lualine_theme = "edge",
  },
  {
    flavour = "mocha", -- latte, frappe, macchiato, mocha
    background = {
      -- :h background
      light = "latte",
      dark = "mocha",
    },
    transparent_background = false, -- disables setting the background color.
    show_end_of_buffer = false,     -- shows the '~' characters after the end of buffers
    term_colors = true,             -- sets terminal colors (e.g. `g:terminal_color_0`)
    dim_inactive = {
      enabled = false,              -- dims the background color of inactive window
      shade = "dark",
      percentage = 0.15,            -- percentage of the shade to apply to the inactive window
    },
    no_italic = false,              -- Force no italic
    no_bold = false,                -- Force no bold
    no_underline = false,           -- Force no underline
    styles = {
      -- Handles the styles of general hi groups (see `:h highlight-args`):
      comments = { "italic" }, -- Change the style of comments
      conditionals = { "italic" },
      loops = {},
      functions = {},
      keywords = {},
      strings = {},
      variables = {},
      numbers = {},
      booleans = {},
      properties = {},
      types = {},
      operators = {},
    },
    color_overrides = {},
    custom_highlights = {},
    integrations = {
      cmp = true,
      gitsigns = true,
      nvimtree = true,
      treesitter = true,
      notify = false,
      mini = {
        enabled = true,
        indentscope_color = "",
      },
      -- For more plugins integrations please scroll down (https://github.com/catppuccin/nvim#integrations)
    },
  },
}



if vim.g.neovide then
  lvim.transparent_window = true
  return
end

if lvim.colorscheme == themes.sonokai then
  vim.g.sonokai_style = "espresso"
  vim.g.sonokai_style = "shusia"
  vim.g.sonokai_style = "default"
end

if lvim.colorscheme == themes.edge then
  vim.g.airline_theme = extra_opts.edge.airline_theme
  vim.g.edge_style = extra_opts.edge.styles.aura
  lvim.builtin.lualine.options.theme = extra_opts.edge.lualine_theme
end

if lvim.colorscheme == themes.ayu then
  vim.g.ayucolor = extra_opts.ayu.styles.mirage
end
if vim.g.neovide then
  vim.opt.guifont = "MonoLisa:h24"

  vim.g.neovide_transparency = 1
  vim.g.transparency = 0.8
  vim.g.neovide_scroll_animation_length = 0.3
  vim.g.neovide_refresh_rate = 60
  vim.g.neovide_confirm_quit = true
  vim.g.neovide_input_macos_alt_is_meta = false
end

lvim.log.level = "warn"
lvim.format_on_save = true

lvim.builtin.treesitter.ensure_installed = {
  "bash",
  "c",
  "javascript",
  "json",
  "lua",
  "python",
  "typescript",
  "tsx",
  "css",
  "rust",
  "java",
  "yaml",
  "vue",
}

lvim.builtin.terminal.active = true
lvim.builtin.nvimtree.setup.view.side = "left"
lvim.builtin.nvimtree.setup.renderer.icons.show.git = true
lvim.builtin.nvimtree.setup.filters.custom = {}
lvim.builtin.nvimtree.setup.view.width = 45

lvim.builtin.treesitter.ignore_install = {}
lvim.builtin.treesitter.highlight.enabled = true

lvim.builtin.project.detection_methods = { "lsp", "pattern" }
lvim.builtin.project.patterns = {
  ".git",
  "package-lock.json",
  "yarn.lock",
  "package.json",
  "requirements.txt",
}

vim.opt.shell = "/bin/zsh"
vim.opt.relativenumber = true
lvim.format_on_save = true

vim.o.linebreak = true
vim.o.wrap = false
vim.o.sessionoptions = "blank,buffers,curdir,folds,help,tabpages,winsize,winpos,terminal,localoptions"

lvim.builtin.telescope.defaults.path_display = {
  shorten = 4,
}
local kind = {}

kind.cmp_kind = {
  Class = " ",
  Color = " ",
  Constant = "",
  Constructor = " ",
  Default = " ",
  Enum = "練",
  EnumMember = " ",
  Event = " ",
  Field = "ﰠ ",
  File = " ",
  Folder = " ",
  Function = " ",
  Interface = " ",
  Keyword = " ",
  Method = "ƒ ",
  Module = " ",
  Operator = " ",
  Property = " ",
  Reference = "",
  Snippet = " ", -- " "," "
  Struct = "פּ ",
  Text = " ",
  TypeParameter = "  ",
  Unit = "塞",
  Value = " ",
  Variable = " ",
}

kind.icons = {
  error = " ",
  warn = " ",
  info = "",
  hint = " ",
  code_action = "",
  test = "",
  docs = "",
  clock = " ",
  calendar = " ",
  buffer = " ",
  settings = " ",
  ls_inactive = "轢",
  ls_active = "歷",
  question = "",
  screen = "冷",
  dart = " ",
  config = " ",
  git = "",
  magic = " ",
  exit = " ",
  repo = "",
  term = " ",
}

kind.nvim_tree_icons = {
  default = "",
  symlink = "",
  git = {
    unstaged = "",
    staged = "",
    unmerged = "",
    renamed = "➜",
    untracked = "",
    deleted = "",
    ignored = "◌",
  },
  folder = {
    arrow_closed = "",
    arrow_open = "",
    default = "",
    open = "",
    empty = "",
    empty_open = "",
    symlink = "",
    symlink_open = "",
  },
}

kind.symbols_outline = {
  File = "",
  Module = "",
  Namespace = "",
  Package = "",
  Class = "",
  Method = "ƒ",
  Property = "",
  Field = "",
  Constructor = "",
  Enum = "練",
  Interface = "ﰮ",
  Function = "",
  Variable = "",
  Constant = "",
  String = "𝓐",
  Number = "#",
  Boolean = "⊨",
  Array = "",
  Object = "⦿",
  Key = "",
  Null = "NULL",
  EnumMember = "",
  Struct = "פּ",
  Event = "",
  Operator = "",
  TypeParameter = "𝙏",
}

kind.todo_comments = {
  FIX = "律",
  TODO = " ",
  HACK = " ",
  WARN = "裂",
  PERF = "龍",
  NOTE = " ",
  ERROR = " ",
  REFS = "",
}

kind.numbers = {
  " ",
  " ",
  " ",
  " ",
  " ",
  " ",
  " ",
  " ",
  " ",
  " ",
}

kind.file_icons = {
  Brown = { "" },
  Aqua = { "" },
  LightBlue = { "", "" },
  Blue = { "", "", "", "", "", "", "", "", "", "", "", "", "" },
  DarkBlue = { "", "" },
  Purple = { "", "", "", "", "" },
  Red = { "", "", "", "", "", "" },
  Beige = { "", "", "" },
  Yellow = { "", "", "λ", "", "" },
  Orange = { "", "" },
  DarkOrange = { "", "", "", "", "" },
  Pink = { "", "" },
  Salmon = { "" },
  Green = { "", "", "", "", "", "" },
  LightGreen = { "", "", "", "﵂" },
  White = { "", "", "", "", "", "" },
}
lvim.leader = "space"

lvim.keys.normal_mode["<C-s>"] = ":w<cr>"
lvim.keys.normal_mode["<S-l>"] = ":BufferLineCycleNext<CR>"
lvim.keys.normal_mode["<S-h>"] = ":BufferLineCyclePrev<CR>"
lvim.keys.insert_mode["jj"] = "<ESC>"
lvim.keys.insert_mode["kk"] = "<ESC>"
vim.keymap.set('n', 'gn', ":tabe %<CR>")

lvim.lsp.buffer_mappings.normal_mode["gr"] = {
  ":lua require'telescope.builtin'.lsp_references()<cr>",
  kind.cmp_kind.Reference .. " Find references"
}

lvim.lsp.buffer_mappings.normal_mode["gd"] = {
  ":lua vim.lsp.buf.definition()<cr>",
  -- ":lua require'telescope.builtin'.lsp_definitions()<cr>",
  kind.cmp_kind.Reference .. " Definitions"
}

lvim.lsp.buffer_mappings.normal_mode["gD"] = {
  ":lua vim.lsp.buf.type_definition()<cr>",
  kind.cmp_kind.Reference .. " Type Definition"
}

lvim.lsp.buffer_mappings.normal_mode["gf"] = {
  ":Telescope frecency<cr>",
  kind.cmp_kind.Reference .. " Telescope Frecency"
}


local wk = lvim.builtin.which_key

wk.mappings["a"] = { ":Alpha<cr>", kind.icons.screen .. " Dashboard" }

wk.mappings["S"] = {
  name = " persistence.nvim",
  s = { "<cmd>lua require('persistence').load()<cr>", kind.icons.clock .. " Reload last session for dir" },
  l = { "<cmd>lua require('persistence').load({ last = true })<cr>", kind.icons.clock .. " Restore last session" },
  Q = { "<cmd>lua require('persistence').stop()<cr>", kind.icons.exit .. " Quit without saving session" },
}

wk.mappings["l"]["t"] = { ":LvimToggleFormatOnSave<cr>", kind.symbols_outline.File .. " Toggle format-on-save" }
wk.mappings["l"]["R"] = { ":LspRestart<cr>", kind.icons.exit .. " Restart" }

wk.mappings["s"]["w"] = {
  "<cmd>lua require('telescope.builtin').grep_string({ search = vim.fn.expand('<cword>') })<cr>",
  ' ' .. kind.cmp_kind.EnumMember .. " Search Word Under Cursor"
}

wk.mappings["m"] = {
  name = ' ' .. kind.todo_comments.PERF .. " Harpoon",
  m = { ":lua require('harpoon.mark').add_file()<cr>", "Mark file" },
  t = { ":lua require('harpoon.ui').toggle_quick_menu()<cr>", "Toggle UI" },
  a = { ":lua require('harpoon.ui').nav_file(1)<cr>", "Goto mark 1" },
  s = { ":lua require('harpoon.ui').nav_file(2)<cr>", "Goto mark 2" },
  d = { ":lua require('harpoon.ui').nav_file(3)<cr>", "Goto mark 3" },
  f = { ":lua require('harpoon.ui').nav_file(4)<cr>", "Goto mark 4" },
  g = { ":lua require('harpoon.ui').nav_file(5)<cr>", "Goto mark 5" },
  q = { ":lua require('harpoon.ui').nav_file(6)<cr>", "Goto mark 6" },
  w = { ":lua require('harpoon.ui').nav_file(7)<cr>", "Goto mark 7" },
  e = { ":lua require('harpoon.ui').nav_file(8)<cr>", "Goto mark 8" },
  r = { ":lua require('harpoon.ui').nav_file(9)<cr>", "Goto mark 9" },
  n = { ":lua require('harpoon.ui').nav_next()<cr>", "Next file" },
  p = { ":lua require('harpoon.ui').nav_prev()<cr>", "Prev file" },
}

wk.mappings["W"] = {
  name = ' ' .. kind.icons.screen .. " Window Ctrl",
  h = { '<C-w>|', 'Maximize window horizontally (|)' },
  v = { '<C-w>_', 'Maximize window vertically (_)' },
  ['='] = { '<C-w>=', 'Resize windows equally' },
  s = { ":lua require('telescope-tabs').list_tabs()<cr>", 'Search Tabs' },
}

wk.mappings["G"] = {
  name = ' ' .. kind.icons.hint .. " Github Copilot",
  a = { ":lua require('copilot.suggestion').accept()<cr>", "Accept" },
  n = { ":lua require('copilot.suggestion').next()<cr>", "Next" },
  N = { ":lua require('copilot.suggestion').prev()<cr>", "Prev" },
  d = { ":lua require('copilot.suggestion').dismiss()<cr>", "Dismiss" },
  t = { ":lua require('copilot.suggestion').toggle_auto_trigger()<cr>", "Toggle Auto Trigger" },
}

wk.mappings['x'] = { ":xa<cr>", "Save All and Quit", }

wk.mappings["t"] = {
  name = ' Telescope',
  p = { ':Telescope projects<cr>', 'Projects' }, -- requires telescope-project.nvim plugin
  r = { ':Telescope resume<cr>', 'Resume' },
}
vim.tbl_extend('keep', lvim.builtin.lualine.sections.lualine_a, { "filename" })
lvim.builtin.lualine.sections.lualine_a = { "filename" }
local lvim_lsp = require('lvim.lsp')
local ts = require('typescript')

-- configure tsserver server manually.
vim.list_extend(lvim.lsp.automatic_configuration.skipped_servers, { "tsserver" })

local common_on_attach = lvim_lsp.common_on_attach
local common_capabilities = lvim_lsp.common_capabilities()

lvim.lsp.on_attach_callback = function(client, bufnr)
  --if lvim.colorscheme == 'gruvbox' then
  --  -- change coloring of errors so I can actually read them with gruvbox
  --  vim.cmd(':hi DiagnosticError guifg=#de5b64 guibg=#1C1C1C')
  --  vim.cmd(':hi DiagnosticWarn guifg=DarkOrange ctermfg=DarkYellow')
  --  vim.cmd(':hi DiagnosticInfo guifg=Cyan ctermfg=Cyan')
  --  vim.cmd(':hi DiagnosticHint guifg=White ctermfg=White')
  --end
end


-- Typescript config using typescript.nvim
ts.setup({
  server = {
    root_dir = require('lspconfig.util').root_pattern('.git'),
    capabilities = common_capabilities,
    on_attach = common_on_attach,
  },
})

-- -- Keeping this here for reference
-- require("lvim.lsp.manager").setup("tsserver", {
--   root_dir = require('lspconfig.util').root_pattern('.git'),
--   on_attach = common_on_attach,
--   filetypes = { "typescript", "typescriptreact", "typescript.tsx", "javascript" },
--   cmd = {
--     "typescript-language-server",
--     "--stdio",
--   },
-- })

local formatters = require "lvim.lsp.null-ls.formatters"
formatters.setup({
  {
    command = "prettierd",
    filetypes = {
      "javascript",
      "javascriptreact",
      "typescript",
      "typescriptreact",
      "vue",
      "css",
      "scss",
      "less",
      "html",
      "yaml",
      "markdown",
      "markdown.mdx",
      "graphql",
      "handlebars",
      "json",
    }
  },
})

local linters = require "lvim.lsp.null-ls.linters"
linters.setup({
  {
    command = "eslint_d",
    filetypes = { "javascript", "typescript", "typescriptreact", "json", "go" }
  },
})

lvim.lsp.diagnostics.float.max_width = 120
lvim.lsp.diagnostics.float.focusable = true

lvim.builtin.cmp.formatting.source_names["copilot"] = "(Copilot)"
table.insert(lvim.builtin.cmp.sources, 1, { name = "copilot" })
lvim.builtin.alpha.active = true
lvim.builtin.alpha.mode = "custom"

local header = {
  type = "text",
  val = require("user.banners").dashboard(),
  opts = {
    position = "center",
    hl = "Comment",
  },
}

local plugins = ""
local date = ""
if vim.fn.has "linux" == 1 or vim.fn.has "mac" == 1 then
  -- -- this no longer works with packer, need to modify to work with lazy.nvim
  -- local handle = io.popen 'fd -d 2 . $HOME"/.local/share/lunarvim/site/pack/packer" | grep pack | wc -l | tr -d "\n" '
  -- plugins = handle:read "*a"
  -- handle:close()

  local thingy = io.popen 'echo "$(date +%a) $(date +%d) $(date +%b)" | tr -d "\n"'
  date = thingy:read "*a"
  thingy:close()
  plugins = plugins:gsub("^%s*(.-)%s*$", "%1")
else
  plugins = "N/A"
  date = "  whatever "
end

local plugin_count = {
  type = "text",
  val = "└─ " .. kind.cmp_kind.Module .. " " .. plugins .. " plugins in total ─┘",
  opts = {
    position = "center",
    hl = "String",
  },
}

local heading = {
  type = "text",
  val = "┌─ " .. kind.icons.calendar .. " Today is " .. date .. " ─┐",
  opts = {
    position = "center",
    hl = "String",
  },
}

local fortune = require "alpha.fortune" ()
-- fortune = fortune:gsub("^%s+", ""):gsub("%s+$", "")
local footer = {
  type = "text",
  val = fortune,
  opts = {
    position = "center",
    hl = "Comment",
    hl_shortcut = "Comment",
  },
}

local function button(sc, txt, keybind)
  local sc_ = sc:gsub("%s", ""):gsub("SPC", "<leader>")

  local opts = {
    position = "center",
    text = txt,
    shortcut = sc,
    cursor = 5,
    width = 24,
    align_shortcut = "right",
    hl_shortcut = "Number",
    hl = "Function",
  }
  if keybind then
    opts.keymap = { "n", sc_, keybind, { noremap = true, silent = true } }
  end

  return {
    type = "button",
    val = txt,
    on_press = function()
      local key = vim.api.nvim_replace_termcodes(sc_, true, false, true)
      vim.api.nvim_feedkeys(key, "normal", false)
    end,
    opts = opts,
  }
end

local buttons = {
  type = "group",
  val = {
    button("f", " " .. kind.cmp_kind.Folder .. " Explore", ":Telescope find_files<CR>"),
    button("e", " " .. kind.cmp_kind.File .. " New file", ":ene <BAR> startinsert <CR>"),
    button("s", " " .. kind.icons.magic .. " Restore", ":lua require('persistence').load()<cr>"),
    button(
      "g",
      " " .. kind.icons.git .. " Git Status",
      ":lua require('lvim.core.terminal')._exec_toggle({cmd = 'lazygit', count = 1, direction = 'float'})<CR>"
    ),
    button("r", " " .. kind.icons.clock .. " Recents", ":Telescope oldfiles<CR>"),
    button("c", " " .. kind.icons.settings .. " Config", ":e ~/.config/lvim/config.lua<CR>"),
    button("C", " " .. kind.cmp_kind.Color .. " Colorscheme Config", ":e ~/.config/lvim/lua/user/colorscheme.lua<CR>"),
    button("q", " " .. kind.icons.exit .. " Quit", ":q<CR>"),
  },
  opts = {
    spacing = 1,
  },
}

local section = {
  header = header,
  buttons = buttons,
  plugin_count = plugin_count,
  heading = heading,
  footer = footer,
}

lvim.builtin.alpha.custom = {
  config = {
    layout = {
      { type = "padding", val = 1 },
      section.header,
      { type = "padding", val = 2 },
      section.heading,
      section.plugin_count,
      { type = "padding", val = 1 },
      section.buttons,
      section.footer,
    },
    opts = {
      margin = 5,
    },
  }
}


-- reload('user.lsp-status')
--
vim.api.nvim_create_autocmd("FileType", {
  pattern = { "zsh" },
  callback = function()
    -- let treesitter use bash highlight for zsh files as well
    require("nvim-treesitter.highlight").attach(0, "bash")
  end,
})

vim.api.nvim_create_autocmd("BufWritePre", {
  pattern = { ".config/lvim/config.lua" },
  command = "PackerCompile",
})

require('kanagawa').setup({
  compile = false,  -- enable compiling the colorscheme
  undercurl = true, -- enable undercurls
  commentStyle = { italic = true },
  functionStyle = {},
  keywordStyle = { italic = true },
  statementStyle = { bold = true },
  typeStyle = {},
  transparent = false,   -- do not set background color
  dimInactive = false,   -- dim inactive window `:h hl-NormalNC`
  terminalColors = true, -- define vim.g.terminal_color_{0,17}
  colors = {             -- add/modify theme and palette colors
    palette = {},
    theme = { wave = {}, lotus = {}, dragon = {}, all = {} },
  },
  overrides = function(colors) -- add/modify highlights
    return {}
  end,
  theme = "wave",  -- Load "wave" theme when 'background' option is not set
  background = {   -- map the value of 'background' option to a theme
    dark = "wave", -- try "dragon" !
    light = "lotus"
  },
})
