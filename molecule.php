<?php
$brackets = [
	'[' => ']',
	'{' => '}',
	'(' => ')'
];
//$molecule = 'K4[ON(SO3)2]2'; // Array ( [S] => 4 [O] => 14 [N] => 2 [K] => 4 )
//$molecule = 'H2O'; // Array ( [H] => 2 [O] => 1 )
//$molecule = 'Mg(OH)2'; // Array ( [O] => 2 [H] => 2 [Mg] => 1 )
//$molecule = 'C6H12O6'; // Array ( [C] => 6 [H] => 12 [O] => 6 )
//$molecule = '[Co(NH3)6]Cl3'; // Array ( [N] => 6 [H] => 18 [Cl] => 3 [Co] => 1 )
$molecule = 'Na2[Fe(CO)4]'; // Array ( [C] => 4 [O] => 4 [Fe] => 1 [Na] => 2 )
// ---------------------
//$molecule = 'Na[PtBrCl(NO2)(NH3)]'; // Array ( [N] => 3 [O] => 2 [H] => 6 [Pt] => 1 [Br] => 1 [Cl] => 1 [Na] => 1 ) ***** N2 H3
//$molecule = '{[Co(NH3)4(OH)2]3Co}(SO4)3{[Co(NH3)4(OH)2]3Co}(SO4)3'; // Array ( [N] => 72 [H] => 234 [O] => 180 [Co] => 18 [S] => 15 )
//$molecule = '(C5H5)Fe(CO)2CH3'; // ['C' => 8, 'H' => 8, 'Fe' => 1, 'O' => 2]],
//$molecule = 'Pd[P(C6H5)3]4'; 	// ['C' => 72, 'H' => 60, 'P' => 4, 'Pd' => 1]],
//$molecule = 'K4[ON(SO3)2]2'; 	// ['K' => 4, 'S' => 4, 'O' => 14, 'N' => 2]],
//$molecule = 'As2{Be4C5[BCo3(CO2)3]2}4Cu5'; 	//['As' => 2, 'Cu' => 5, 'Be' => 16, 'C' => 44, 'B' => 8, 'Co' => 24, 'O' => 48]],
//$molecule = '{[Co(NH3)4(OH)2]3Co}(SO4)3'; 	//['Co' => 4, 'H' => 42, 'N' => 12, 'O' => 18, 'S' => 3]],


$result = [];
do
{
	$endPos = 999;
	foreach($brackets as $char)
	{
		$pos = strpos($molecule, $char);
		$endPos = (($endPos > $pos) && is_int($pos)) ? $pos : $endPos;
	}
	$startChar = $endPos < 999 ? array_search($molecule[$endPos], $brackets) : null;
	if ($endPos !== 999)
	{
		$startPos = strpos($molecule, $startChar);

		//$pattern = "/\' . $startChar . '([^' . $brackets[$startChar] . ']+)\' . $brackets[$startChar] . '/";
		//preg_match($pattern, $molecule, $matches);
		//print_r($matches);

		$actual = strpbrk($molecule, $startChar);

	}
	else
	{
		$startPos = 0;
		$actual = $molecule;
	}

	$length = strlen($actual);
	$actualResult = [];
	for ($i=0; $i<$length; $i++)
	{
		if (ctype_upper($actual[$i]))
		{
			$atom = $actual[$i];
			if (
				!empty($actual[$i + 1])
				&& ctype_lower($actual[$i + 1])
			) {
				$atom .= $actual[$i + 1];
			}

			$actualResult[$atom] = !empty($actualResult[$atom]) ? $actualResult[$atom]++ : 1;
			//print_r($actualResult);
		}
		elseif (
			is_numeric($actual[$i])
			&& (ctype_upper($actual[$i - 1])
			    || ctype_lower($actual[$i - 1])
			)
		) {
			$atomNumber = $actual[$i];
			$atom = ctype_upper($actual[$i - 1]) ? $actual[$i - 1] : $actual[$i - 2] . $actual[$i - 1];
			$actual2 = strpbrk($molecule, $actual[$i]);
			$length2 = strlen($actual2);
			for ($j=1; $j<$length2; $j++)
			{
				if (
				is_numeric($actual2[$j])
				) {
					$atomNumber .= $actual2[$j];
				}
				else
				{
					break;
				}
			}

			$actualResult[$atom] = $actualResult[$atom] * (int)$atomNumber;
		}
		elseif (
			is_numeric($actual[$i])
			&& in_array($actual[$i - 1], $brackets)
		) {
			foreach($actualResult as $key => $value)
			{
				$actualResult[$key] *= $actual[$i];
			}
		}
	}
	//print_r($actualResult);
	foreach($actualResult as $k => $v) {
		if(array_key_exists($k, $result)) {
			$result[$k] += $v;
		} else {
			$result[$k] = $v;
		}
	}
	if (
		$endPos !== 999
		&& !empty($molecule[$endPos + 1])
		&& is_numeric($molecule[$endPos + 1]))
	{
		$endPos++;
	}
	$removableMolecule = substr($molecule, $startPos, $endPos - $startPos + 1);
	//print $removableMolecule . '[]';
	$molecule = str_replace($removableMolecule, '', $molecule);
}
while (!empty($molecule));
print_r($result);
?>