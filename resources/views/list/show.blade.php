public function store(Request $request)
{
    // Validasi input
    $validated = $request->validate([
        'id_divisi' => 'required|exists:divisi,id',
        'issue' => 'required|string',
        'pihak.*' => 'required|exists:divisi,id',
        'resiko.*' => 'nullable|string|max:255',
        'tindakan.*' => 'nullable|string|max:255',
        'pic.*' => 'nullable|string|max:255',
        'peluang' => 'nullable|string',
        'tingkatan' => 'nullable|string',
        'status' => 'nullable|in:OPEN,ON PROGRESS,CLOSE',
        'risk' => 'nullable|in:HIGH,MEDIUM,LOW',
    ]);

    $pihak = json_encode($request->input('pihak'));

    // Buat entri baru dalam database
    ListForm::create([
        'id_divisi' => $request->input('id_divisi'),
        'issue' => $request->input('issue'),
        'pihak' => $pihak,
        'resiko' => json_encode($request->input('resiko')),
        'tindakan' => json_encode($request->input('tindakan')),
        'pic' => json_encode($request->input('pic')),
        'peluang' => $request->input('peluang'),
        'tingkatan' => $request->input('tingkatan'),
        'status' => $request->input('status'),
        'risk' => $request->input('risk'),
    ]);