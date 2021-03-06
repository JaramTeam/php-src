--TEST--
Match expression string jump table
--INI--
opcache.enable=1
opcache.enable_cli=1
opcache.opt_debug_level=0x20000
--SKIPIF--
<?php require_once(__DIR__ . '/../skipif.inc'); ?>
--FILE--
<?php

function test($char) {
    return match ($char) {
        'a' => 'a',
        'b', 'c' => 'b, c',
        'd' => 'd',
        'e', 'f' => 'e, f',
        'g' => 'g',
        'h', 'i' => 'h, i',
    };
}

foreach (range('a', 'i') as $char) {
    var_dump(test($char));
}

--EXPECTF--
$_main:
     ; (lines=15, args=0, vars=1, tmps=2)
     ; (after optimizer)
     ; %s
0000 INIT_FCALL 2 %d string("range")
0001 SEND_VAL string("a") 1
0002 SEND_VAL string("i") 2
0003 V2 = DO_ICALL
0004 V1 = FE_RESET_R V2 0013
0005 FE_FETCH_R V1 CV0($char) 0013
0006 INIT_FCALL 1 %d string("var_dump")
0007 INIT_FCALL 1 %d string("test")
0008 SEND_VAR CV0($char) 1
0009 V2 = DO_UCALL
0010 SEND_VAR V2 1
0011 DO_ICALL
0012 JMP 0005
0013 FE_FREE V1
0014 RETURN int(1)
LIVE RANGES:
     1: 0005 - 0013 (loop)

test:
     ; (lines=9, args=1, vars=1, tmps=0)
     ; (after optimizer)
     ; %s
0000 CV0($char) = RECV 1
0001 MATCH CV0($char) "a": 0002, "b": 0003, "c": 0003, "d": 0004, "e": 0005, "f": 0005, "g": 0006, "h": 0007, "i": 0007, default: 0008
0002 RETURN string("a")
0003 RETURN string("b, c")
0004 RETURN string("d")
0005 RETURN string("e, f")
0006 RETURN string("g")
0007 RETURN string("h, i")
0008 MATCH_ERROR CV0($char)
string(1) "a"
string(4) "b, c"
string(4) "b, c"
string(1) "d"
string(4) "e, f"
string(4) "e, f"
string(1) "g"
string(4) "h, i"
string(4) "h, i"
