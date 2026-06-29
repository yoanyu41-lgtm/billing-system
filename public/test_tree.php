<?php
header('Content-Type: text/plain');
echo "=== TREE e0f6 ===\n";
echo shell_exec('git ls-tree -r e0f6a1505fffeaacaab64dcaa5c016fd47cb7cbe 2>&1');
echo "\n=== TREE 357e ===\n";
echo shell_exec('git ls-tree -r 357e2031cf21a28e77bc44ef80b27dff8b6b683f 2>&1');
