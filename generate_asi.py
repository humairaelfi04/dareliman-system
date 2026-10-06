import os
import xml.etree.ElementTree as ET

def create_drawio(filename, title, lanes, nodes, edges):
    mxfile = ET.Element("mxfile", host="Electron", modified="2023-01-01T00:00:00.000Z", agent="5.0", version="21.1.2", type="device")
    diagram = ET.SubElement(mxfile, "diagram", id="page-1", name="Page-1")
    mxGraphModel = ET.SubElement(diagram, "mxGraphModel", dx="1000", dy="1000", grid="1", gridSize="10", guides="1", tooltips="1", connect="1", arrows="1", fold="1", page="1", pageScale="1", pageWidth="1000", pageHeight="3500", math="0", shadow="0")
    root = ET.SubElement(mxGraphModel, "root")
    
    ET.SubElement(root, "mxCell", id="0")
    ET.SubElement(root, "mxCell", id="1", parent="0")
    
    styles = {
        "document": "shape=document;whiteSpace=wrap;html=1;boundedLbl=1;fillColor=#dae8fc;strokeColor=#6c8ebf;",
        "manualOp": "shape=trapezoid;perimeter=trapezoidPerimeter;whiteSpace=wrap;html=1;fixedSize=1;flipV=1;fillColor=#ffe6cc;strokeColor=#d79b00;",
        "database": "shape=cylinder3;boundedLbl=1;backgroundOutline=1;size=15;whiteSpace=wrap;html=1;fillColor=#d5e8d4;strokeColor=#82b366;",
        "manualInput": "shape=manualInput;size=15;whiteSpace=wrap;html=1;fillColor=#e1d5e7;strokeColor=#9673a6;",
        "process": "rounded=0;whiteSpace=wrap;html=1;fillColor=#fff2cc;strokeColor=#d6b656;",
        "swimlane": "swimlane;whiteSpace=wrap;html=1;",
        "terminal": "rounded=1;whiteSpace=wrap;html=1;arcSize=50;fillColor=#f8cecc;strokeColor=#b85450;"
    }
    
    # Add lanes
    for lane in lanes:
        cell = ET.SubElement(root, "mxCell", id=lane['id'], value=lane['name'], style=styles["swimlane"], parent="1", vertex="1")
        ET.SubElement(cell, "mxGeometry", x=str(lane['x']), y="0", width=str(lane['w']), height="3000", **{"as":"geometry"})
        
    for node in nodes:
        stl = styles.get(node['shape'], styles['process'])
        cell = ET.SubElement(root, "mxCell", id=node['id'], value=node['label'], style=stl, parent=node['lane'], vertex="1")
        ET.SubElement(cell, "mxGeometry", x="65", y=str(node['y']), width="120", height="60", **{"as":"geometry"})
        
    for edge in edges:
        style = "edgeStyle=orthogonalEdgeStyle;rounded=0;orthogonalLoop=1;jettySize=auto;html=1;"
        cell = ET.SubElement(root, "mxCell", id=edge['id'], style=style, parent="1", source=edge['source'], target=edge['target'], edge="1")
        ET.SubElement(cell, "mxGeometry", relative="1", **{"as":"geometry"})
        
    tree = ET.ElementTree(mxfile)
    tree.write(filename, encoding='utf-8', xml_declaration=True)

y = 50
id_cnt = 100

def add_n(n_list, e_list, lane, shape, label, lx, curr_y, prev_id):
    global id_cnt
    nid = str(id_cnt)
    id_cnt += 1
    n_list.append({"id":nid, "lane":lane, "shape":shape, "label":label, "x":lx, "y":curr_y})
    if prev_id:
        e_list.append({"id":"e"+nid, "source":prev_id, "target":nid})
    return nid

# Sistem Berjalan (Connected Single Flow)
lanes1 = [
    {"id":"L1", "name":"Pegawai Sarpras / Bendahara", "x":0, "w":250},
    {"id":"L2", "name":"Media Penyimpanan", "x":250, "w":250},
    {"id":"L3", "name":"Kepala Sekolah", "x":500, "w":250}
]

y = 50; nodes1 = []; edges1 = []

n0 = add_n(nodes1, edges1, "L1", "terminal", "Mulai", 60, y, None); y+=100
n1 = add_n(nodes1, edges1, "L1", "document", "Menerima Bukti Transaksi Aset Baru", 60, y, n0); y+=100
n2 = add_n(nodes1, edges1, "L1", "manualInput", "Ketik Data Aset ke Spreadsheet", 60, y, n1); y+=100
n3 = add_n(nodes1, edges1, "L2", "document", "Google Sheets (Sistem Rekap)", 60, y, n2); y+=120
n4 = add_n(nodes1, edges1, "L1", "manualOp", "Mencetak & Bawa Kartu Pemeliharaan", 60, y, n3); y+=100
n5 = add_n(nodes1, edges1, "L1", "document", "Kartu Fisik Update & Inspeksi Ruangan", 60, y, n4); y+=100
n6 = add_n(nodes1, edges1, "L1", "manualInput", "Ketik Update Lokasi/Kondisi ke Spreadsheet", 60, y, n5); y+=100
n7 = add_n(nodes1, edges1, "L2", "document", "Google Sheets (Update)", 60, y, n6); y+=120
n8 = add_n(nodes1, edges1, "L1", "manualOp", "Seleksi Aset Rusak / Tak Layak", 60, y, n7); y+=100
n9 = add_n(nodes1, edges1, "L1", "manualOp", "Mengajukan Pelepasan Secara Lisan", 60, y, n8); y+=100
n10= add_n(nodes1, edges1, "L3", "manualOp", "Evaluasi Keputusan Secara Lisan", 60, y, n9); y+=100
n11= add_n(nodes1, edges1, "L1", "manualInput", "Update Spreadsheet Transaksi Pelepasan", 60, y, n10); y+=100
n12= add_n(nodes1, edges1, "L1", "manualInput", "Ketik Jadwal Servis AC", 60, y, n11); y+=100
n13= add_n(nodes1, edges1, "L2", "document", "Google Calendar (Jadwal Fisik)", 60, y, n12); y+=120
n14= add_n(nodes1, edges1, "L1", "manualOp", "Kompilasi Laporan Aset Manual", 60, y, n13); y+=100
n15= add_n(nodes1, edges1, "L1", "document", "Laporan Aset Fisik Bulanan", 60, y, n14); y+=100
n16= add_n(nodes1, edges1, "L3", "document", "Menerima & Arsip Laporan Aset", 60, y, n15); y+=100
n17= add_n(nodes1, edges1, "L3", "terminal", "Selesai", 60, y, n16); y+=100

create_drawio(r"c:\Users\USER\dareliman-project\Aliran_Sistem_Berjalan.drawio", "ASI Sistem Berjalan", lanes1, nodes1, edges1)


# Sistem Diusulkan (Connected Single Flow)
lanes2 = [
    {"id":"L1", "name":"Admin Sarpras / Bendahara", "x":0, "w":250},
    {"id":"L2", "name":"Sistem Informasi Manajemen Aset", "x":250, "w":250},
    {"id":"L3", "name":"Kepala Sekolah", "x":500, "w":250}
]

y = 50; nodes2 = []; edges2 = []

n2_0 = add_n(nodes2, edges2, "L1", "terminal", "Mulai", 60, y, None); y+=100
n2_0a = add_n(nodes2, edges2, "L1", "manualInput", "Input Username & Password (Login)", 60, y, n2_0); y+=100
n2_0b = add_n(nodes2, edges2, "L2", "process", "Verifikasi Kredensial Login", 60, y, n2_0a); y+=100
n2_0c = add_n(nodes2, edges2, "L2", "database", "Database Users / Admin", 60, y, n2_0b); y+=140
n2_1 = add_n(nodes2, edges2, "L1", "document", "Terima Bukti Transaksi Aset Baru", 60, y, n2_0c); y+=100
n2_2 = add_n(nodes2, edges2, "L1", "manualInput", "Input Form Tambah Aset di Aplikasi", 60, y, n2_1); y+=100
n2_3 = add_n(nodes2, edges2, "L2", "process", "Sistem Menghitung Penyusutan", 60, y, n2_2); y+=100
n2_4 = add_n(nodes2, edges2, "L2", "database", "Database Assets & Histori", 60, y, n2_3); y+=120
n2_5 = add_n(nodes2, edges2, "L1", "manualInput", "Input Audit Ruangan & Kondisi Fisik", 60, y, n2_4); y+=100
n2_6 = add_n(nodes2, edges2, "L2", "process", "Update Kondisi Status & Lokasi Terpusat", 60, y, n2_5); y+=100
n2_6a = add_n(nodes2, edges2, "L2", "database", "Record Jejak Histori Audit", 60, y, n2_6); y+=140
n2_7 = add_n(nodes2, edges2, "L1", "manualInput", "Ketik Detail Permohonan Pelepasan", 60, y, n2_6a); y+=100
n2_8 = add_n(nodes2, edges2, "L2", "process", "Tampilkan Notifikasi Request Approval", 60, y, n2_7); y+=100
n2_9 = add_n(nodes2, edges2, "L3", "manualInput", "Review & Klik Setujui/Tolak Pelepasan", 60, y, n2_8); y+=100
n2_10 = add_n(nodes2, edges2, "L2", "process", "Eksekusi Ubah Status Aset Internal", 60, y, n2_9); y+=100
n2_11 = add_n(nodes2, edges2, "L2", "database", "Database Disposal & Arsip Aset", 60, y, n2_10); y+=120
n2_12 = add_n(nodes2, edges2, "L1", "manualInput", "Input Riwayat Servis Aset Terkini", 60, y, n2_11); y+=100
n2_13 = add_n(nodes2, edges2, "L2", "process", "Kalkulasi Algoritma Jadwal Servis Mendatang", 60, y, n2_12); y+=100
n2_13a = add_n(nodes2, edges2, "L2", "database", "Update DB Jadwal Maintenance", 60, y, n2_13); y+=140
n2_14 = add_n(nodes2, edges2, "L1", "manualInput", "Masukkan Filter Cetak Laporan (Tgl)", 60, y, n2_13a); y+=100
n2_15 = add_n(nodes2, edges2, "L2", "process", "Kueri Data & Render DomPDF / Excel", 60, y, n2_14); y+=100
n2_16 = add_n(nodes2, edges2, "L1", "document", "Unduhan File Laporan Digital", 60, y, n2_15); y+=100
n2_17 = add_n(nodes2, edges2, "L3", "document", "Kepala Sekolah Menerima File Analisis Aset", 60, y, n2_16); y+=100
n2_18 = add_n(nodes2, edges2, "L3", "terminal", "Selesai", 60, y, n2_17); y+=100

create_drawio(r"c:\Users\USER\dareliman-project\Aliran_Sistem_Diusulkan.drawio", "ASI Sistem Diusulkan", lanes2, nodes2, edges2)
print("Files generated as connected chains!")
