<?php
$file = 'd:/laragon/www/stok/sidebar_menu.php';
$content = file_get_contents($file);

$menuSnippet = '
		// jika menu permintaan barang (tampil data / form entri) dipilih, menu permintaan aktif
		if (isset($_GET["module"]) && ($_GET["module"] == "permintaan" || $_GET["module"] == "form_entri_permintaan")) { ?>
			<li class="nav-item active">
				<a href="?module=permintaan">
					<i class="fas fa-file-invoice"></i>
					<p>Permintaan Barang</p>
				</a>
			</li>
		<?php
		}
		// jika tidak dipilih, menu permintaan tidak aktif
		else { ?>
			<li class="nav-item">
				<a href="?module=permintaan">
					<i class="fas fa-file-invoice"></i>
					<p>Permintaan Barang</p>
				</a>
			</li>
		<?php
		}
';

$regularUserSnippet = '
	// jika hak akses = User Biasa
	else {
		// pengecekan menu aktif
		// jika menu dashboard dipilih, menu dashboard aktif
		if (isset($_GET["module"]) && $_GET["module"] == "dashboard") { ?>
			<li class="nav-item active">
				<a href="?module=dashboard">
					<i class="fas fa-home"></i>
					<p>Dashboard</p>
				</a>
			</li>
		<?php
		}
		// jika tidak dipilih, menu dashboard tidak aktif
		else { ?>
			<li class="nav-item">
				<a href="?module=dashboard">
					<i class="fas fa-home"></i>
					<p>Dashboard</p>
				</a>
			</li>
		<?php
		}
		?>
			<li class="nav-section">
				<span class="sidebar-mini-icon">
					<i class="fa fa-ellipsis-h"></i>
				</span>
				<h4 class="text-section">Transaksi</h4>
			</li>
		<?php
' . $menuSnippet . '
	}
';

$targetString = '		// jika tidak dipilih, menu barang keluar tidak aktif
		else { ?>
			<li class="nav-item">
				<a href="?module=barang_keluar">
					<i class="fas fa-sign-out-alt"></i>
					<p>Barang Keluar</p>
				</a>
			</li>
		<?php
		}';

$content = str_replace($targetString, $targetString . "\n" . $menuSnippet, $content);

$endTarget = '<?php
		}
	}
}
?>';

$endReplace = '<?php
		}
	}
' . $regularUserSnippet . '
}
?>';

$content = str_replace($endTarget, $endReplace, $content);
file_put_contents($file, $content);
echo "Patch applied.";
