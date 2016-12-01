#!/usr/bin/env python
import sys

if __name__ == "__main__":
    if len(sys.argv) < 2:
        pass
    elif sys.argv[1] == "crawl-file-types":
        print(".page")
    elif sys.argv[1] == "crawl-source":
        pass
