import sys
import json
import os
import openpyxl
from openpyxl.styles import Font, PatternFill, Alignment, Border, Side
from openpyxl.utils import get_column_letter

def create_excel(input_json_path, output_excel_path):
    if not os.path.exists(input_json_path):
        print(f"Error: Input JSON file {input_json_path} not found.")
        sys.exit(1)

    with open(input_json_path, "r", encoding="utf-8") as f:
        data = json.load(f)

    wb = openpyxl.Workbook()
    wb.remove(wb.active)  # Remove default empty sheet

    # Define color scheme (SIKERA Emerald Teal & Slate theme)
    header_fill = PatternFill(start_color="0F766E", end_color="0F766E", fill_type="solid")
    header_font = Font(name="Calibri", size=11, bold=True, color="FFFFFF")
    
    sub_fill = PatternFill(start_color="14B8A6", end_color="14B8A6", fill_type="solid")
    sub_font = Font(name="Calibri", size=10, bold=True, color="FFFFFF")

    accent_fill = PatternFill(start_color="F0FDFA", end_color="F0FDFA", fill_type="solid")
    zebra_fill = PatternFill(start_color="F8FAFC", end_color="F8FAFC", fill_type="solid")
    
    bold_font = Font(name="Calibri", size=10, bold=True)
    normal_font = Font(name="Calibri", size=10)
    
    thin_border_side = Side(border_style="thin", color="E2E8F0")
    cell_border = Border(left=thin_border_side, right=thin_border_side, top=thin_border_side, bottom=thin_border_side)
    
    center_align = Alignment(horizontal="center", vertical="center")
    left_align = Alignment(horizontal="left", vertical="center")
    right_align = Alignment(horizontal="right", vertical="center")

    # ==========================================
    # SHEET 1: Rekap Nilai & N-Gain
    # ==========================================
    ws1 = wb.create_sheet(title="Rekap Nilai & N-Gain")
    ws1.views.sheetView[0].showGridLines = True

    headers1 = [
        "No.", "Timestamp", "NIM", "Nama Responden", "Inisial",
        "Gender", "Usia", "Program Studi", "Fakultas", "Agama", "Pendidikan Terakhir",
        "Target Ujian", "Skor Pre-Test", "Skor Post-Test", "Peningkatan (Gain)",
        "N-Gain Score", "Kategori Efektivitas", "Benar", "Total Soal"
    ]

    ws1.append(headers1)
    for col_idx in range(1, len(headers1) + 1):
        cell = ws1.cell(row=1, column=col_idx)
        cell.fill = header_fill
        cell.font = header_font
        cell.alignment = center_align
        cell.border = cell_border
    ws1.row_dimensions[1].height = 26

    rekap_rows = data.get("rekap_rows", [])
    for r_idx, row in enumerate(rekap_rows, start=2):
        ws1.append([
            row.get("no", r_idx - 1),
            row.get("timestamp", "-"),
            row.get("nim", "-"),
            row.get("name", "-"),
            row.get("initials", "-"),
            row.get("gender", "-"),
            row.get("usia", "-"),
            row.get("prodi", "-"),
            row.get("fakultas", "-"),
            row.get("agama", "-"),
            row.get("pendidikan", "-"),
            row.get("module_target", "-"),
            row.get("pre_score", 0),
            row.get("post_score", 0),
            row.get("gain", 0),
            row.get("n_gain", 0.0),
            row.get("effectiveness", "-"),
            row.get("correct_count", 0),
            row.get("total_questions", 0)
        ])

        fill = zebra_fill if r_idx % 2 == 0 else PatternFill(fill_type=None)
        for c_idx in range(1, len(headers1) + 1):
            c = ws1.cell(row=r_idx, column=c_idx)
            c.border = cell_border
            if fill.fill_type:
                c.fill = fill
            
            # Alignments & formats
            if c_idx in [1, 2, 5, 6, 7, 12, 17, 18, 19]:
                c.alignment = center_align
            elif c_idx in [13, 14, 15, 16]:
                c.alignment = center_align
                c.font = bold_font
            else:
                c.alignment = left_align
        ws1.row_dimensions[r_idx].height = 20

    # Auto-adjust column widths
    for col in ws1.columns:
        max_len = max(len(str(cell.value or '')) for cell in col)
        col_letter = get_column_letter(col[0].column)
        ws1.column_dimensions[col_letter].width = max(max_len + 3, 11)

    # ==========================================
    # SHEET 2: Tabulasi Jawaban (A-B-C-D)
    # ==========================================
    ws2 = wb.create_sheet(title="Tabulasi Jawaban (Pilihan)")
    ws2.views.sheetView[0].showGridLines = True

    questions = data.get("questions", [])
    q_count = len(questions)

    headers2 = ["No.", "NIM", "Inisial", "Nama Responden", "Target Ujian", "Skor Post-Test"]
    for i, q in enumerate(questions, start=1):
        headers2.append(f"Soal_{i}")

    ws2.append(headers2)
    for col_idx in range(1, len(headers2) + 1):
        cell = ws2.cell(row=1, column=col_idx)
        cell.fill = header_fill
        cell.font = header_font
        cell.alignment = center_align
        cell.border = cell_border
    ws2.row_dimensions[1].height = 26

    jawaban_rows = data.get("jawaban_rows", [])
    for r_idx, row in enumerate(jawaban_rows, start=2):
        row_vals = [
            row.get("no", r_idx - 1),
            row.get("nim", "-"),
            row.get("initials", "-"),
            row.get("name", "-"),
            row.get("module_target", "-"),
            row.get("post_score", 0)
        ]
        row_vals.extend(row.get("answers", []))
        ws2.append(row_vals)

        fill = zebra_fill if r_idx % 2 == 0 else PatternFill(fill_type=None)
        for c_idx in range(1, len(row_vals) + 1):
            c = ws2.cell(row=r_idx, column=c_idx)
            c.border = cell_border
            if fill.fill_type:
                c.fill = fill
            if c_idx > 6 or c_idx in [1, 2, 3, 5, 6]:
                c.alignment = center_align
                if c_idx > 6:
                    c.font = bold_font
            else:
                c.alignment = left_align
        ws2.row_dimensions[r_idx].height = 20

    for col in ws2.columns:
        max_len = max(len(str(cell.value or '')) for cell in col)
        col_letter = get_column_letter(col[0].column)
        ws2.column_dimensions[col_letter].width = max(max_len + 3, 10)

    # ==========================================
    # SHEET 3: Tabulasi Biner (1 / 0) SPSS Ready
    # ==========================================
    ws3 = wb.create_sheet(title="Tabulasi Biner (1-0 SPSS)")
    ws3.views.sheetView[0].showGridLines = True

    headers3 = ["No.", "NIM", "Inisial", "Nama Responden", "Target Ujian", "Total Benar"]
    for i in range(1, q_count + 1):
        headers3.append(f"Q_{i}")

    ws3.append(headers3)
    for col_idx in range(1, len(headers3) + 1):
        cell = ws3.cell(row=1, column=col_idx)
        cell.fill = header_fill
        cell.font = header_font
        cell.alignment = center_align
        cell.border = cell_border
    ws3.row_dimensions[1].height = 26

    biner_rows = data.get("biner_rows", [])
    for r_idx, row in enumerate(biner_rows, start=2):
        row_vals = [
            row.get("no", r_idx - 1),
            row.get("nim", "-"),
            row.get("initials", "-"),
            row.get("name", "-"),
            row.get("module_target", "-"),
            row.get("correct_count", 0)
        ]
        row_vals.extend(row.get("binary_scores", []))
        ws3.append(row_vals)

        fill = zebra_fill if r_idx % 2 == 0 else PatternFill(fill_type=None)
        for c_idx in range(1, len(row_vals) + 1):
            c = ws3.cell(row=r_idx, column=c_idx)
            c.border = cell_border
            if fill.fill_type:
                c.fill = fill
            if c_idx > 6 or c_idx in [1, 2, 3, 5, 6]:
                c.alignment = center_align
            else:
                c.alignment = left_align
        ws3.row_dimensions[r_idx].height = 20

    # Summary Row: Rata-Rata Tingkat Kesukaran (% Benar)
    if biner_rows and q_count > 0:
        summary_row_idx = len(biner_rows) + 2
        summary_vals = ["", "", "", "Tingkat Kesukaran (% Benar)", "", ""]
        total_respondents = len(biner_rows)
        for q_i in range(q_count):
            q_sum = sum(r.get("binary_scores", [])[q_i] for r in biner_rows if len(r.get("binary_scores", [])) > q_i)
            pct = round((q_sum / total_respondents) * 100, 1) if total_respondents > 0 else 0
            summary_vals.append(f"{pct}%")
        ws3.append(summary_vals)
        ws3.row_dimensions[summary_row_idx].height = 22
        for c_idx in range(1, len(summary_vals) + 1):
            c = ws3.cell(row=summary_row_idx, column=c_idx)
            c.fill = sub_fill
            c.font = sub_font
            c.border = cell_border
            c.alignment = center_align

    for col in ws3.columns:
        max_len = max(len(str(cell.value or '')) for cell in col)
        col_letter = get_column_letter(col[0].column)
        ws3.column_dimensions[col_letter].width = max(max_len + 3, 10)

    # ==========================================
    # SHEET 4: Bank Soal & Kunci Jawaban
    # ==========================================
    ws4 = wb.create_sheet(title="Bank Soal & Kunci")
    ws4.views.sheetView[0].showGridLines = True

    headers4 = [
        "No.", "Modul Target", "Teks Pertanyaan",
        "Pilihan A", "Pilihan B", "Pilihan C", "Pilihan D",
        "Kunci Jawaban", "Pembahasan Ilmiah & Analisis Medis"
    ]

    ws4.append(headers4)
    for col_idx in range(1, len(headers4) + 1):
        cell = ws4.cell(row=1, column=col_idx)
        cell.fill = header_fill
        cell.font = header_font
        cell.alignment = center_align
        cell.border = cell_border
    ws4.row_dimensions[1].height = 26

    for r_idx, q in enumerate(questions, start=2):
        opts = {opt.get("label", "").upper(): opt.get("text", "") for opt in q.get("options", [])}
        ws4.append([
            r_idx - 1,
            f"Modul {q.get('module_target', '-')}",
            q.get("question_text", ""),
            opts.get("A", "-"),
            opts.get("B", "-"),
            opts.get("C", "-"),
            opts.get("D", "-"),
            q.get("correct_answer", "-"),
            q.get("explanation", "-")
        ])

        fill = zebra_fill if r_idx % 2 == 0 else PatternFill(fill_type=None)
        for c_idx in range(1, len(headers4) + 1):
            c = ws4.cell(row=r_idx, column=c_idx)
            c.border = cell_border
            if fill.fill_type:
                c.fill = fill
            if c_idx in [1, 2, 8]:
                c.alignment = center_align
                if c_idx == 8:
                    c.font = bold_font
            else:
                c.alignment = left_align
        ws4.row_dimensions[r_idx].height = 24

    for col in ws4.columns:
        max_len = max(len(str(cell.value or '')) for cell in col)
        col_letter = get_column_letter(col[0].column)
        ws4.column_dimensions[col_letter].width = min(max(max_len + 3, 12), 60)

    # Save to file
    wb.save(output_excel_path)
    print("SUCCESS")

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print("Usage: python export_posttest_excel.py <input_json> <output_xlsx>")
        sys.exit(1)
    create_excel(sys.argv[1], sys.argv[2])
