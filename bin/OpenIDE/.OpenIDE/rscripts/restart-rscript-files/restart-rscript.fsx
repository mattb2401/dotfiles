open System
open System.IO

let printReactPatterns () =
    printfn "'command-at-caret' 'restart-rscript-from-caret'"

let rec readOutput lines =
    match Console.ReadLine() with
     | "end-of-conversation" -> lines
     | (line) -> readOutput (Array.append lines [|line|])

let handleEvent (event, globalProfile, localProfile) =
    printfn "request|editor get-caret"
    let file = (readOutput [||]).[0].Split('|').[0]
    let matches =
        file.Split(Path.DirectorySeparatorChar)
         |> Array.filter (fun line -> line.EndsWith("-files"))
         |> Array.map (fun itm -> itm.Substring(0, itm.Length-6))
    let name =
        if (matches |> Array.length) > 0 then
            matches
             |> Seq.last
             |> fun itm -> file.Substring(0, file.IndexOf(itm+"-files"))+itm
             |> fun file -> Directory.GetFiles(Path.GetDirectoryName(file), Path.GetFileName(file)+"*")
             |> Seq.head
        else file
    printfn "event|'codemodel' 'raw-filesystem-change-filechanged' '%s'" name
    printfn "Restarting reactive script %s" (Path.GetFileNameWithoutExtension name)

[<EntryPoint>]
let main args = 
    match args with
     | (a) when a.Length.Equals(1) &&  a.[0].Equals("reactive-script-reacts-to") -> printReactPatterns()
     | (a) when a.Length.Equals(3) -> handleEvent(a.[0], a.[1], a.[2])
     | (a) -> ()
    0