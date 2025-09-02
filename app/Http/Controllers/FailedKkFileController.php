<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasApiUrl;
use App\Models\FailedKkFile;
use App\Models\RW;
use Illuminate\Http\Request;

class FailedKkFileController extends Controller
{
    use HasApiUrl;

    private function getApiToken()
    {
        return session('api_token');
    }

    private function getUserId()
    {
        $apiUser = session('api_user');
        return $apiUser['id'] ?? null;
    }

    private function getRWbyUuid($rw_uuid)
    {
        $apiUrl = $this->getApiUrl();
        $apiToken = $this->getApiToken();
        $userId = $this->getUserId();

        if (!$apiToken || !$userId) {
            return null;
        }

        try {
            $response = \Http::withToken($apiToken)
                ->timeout(30)
                ->get("{$apiUrl}/rw/{$rw_uuid}", [
                    'user_id' => $userId,
                ]);

            if ($response->successful()) {
                return $response->json()['data'];
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to fetch RW from API', [
                'rw_uuid' => $rw_uuid,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    public function show($desa_id, $rw_id, $file_id)
    {
        $rwLocal = RW::where('id', $rw_id)->first();

        if (!$rwLocal) {
            return redirect()->back()->with('error', 'Local RW not found.');
        }

        $rwData = $this->getRWbyUuid($rwLocal->uuid);


        if (!$rwData) {
            // Fallback to local RW data if API fails
            return redirect()->route('rw.index', [$desa_id, $rw_id])
                ->with('success', 'File marked as manually processed.');
        }

        $rw = (object) $rwData;
        $rw->local = $rwLocal;



        $failedFile = FailedKkFile::where('rw_id', $rw_id)->findOrFail($file_id);

        return view('failed-files.show', compact('rw', 'failedFile'));
    }

    public function markAsProcessed($desa_id, $rw_id, $file_id)
    {
        $failedFile = FailedKkFile::where('rw_id', $rw_id)->findOrFail($file_id);

        $failedFile->update([
            'manually_processed' => true,
            'processed_at' => now(),
        ]);

        $rwLocal = RW::where('id', $rw_id)->first();

        if (!$rwLocal) {
            return redirect()->back()->with('error', 'Local RW not found.');
        }

        $rw = $this->getRWbyUuid($rwLocal->uuid);

        if (!$rw || !isset($rw) || !isset($rw['id'])) {
            // Fallback to local RW data if API fails
            return redirect()->route('rw.index', [$desa_id, $rw_id])
                ->with('success', 'File marked as manually processed.');
        }

        return redirect()->route('rw.index', [$desa_id, $rw['id']])
            ->with('success', 'File marked as manually processed.');
    }

    public function destroy($desa_id, $rw_id, $file_id)
    {
        $failedFile = FailedKkFile::where('rw_id', $rw_id)->findOrFail($file_id);
        $failedFile->delete();

        $rwLocal = RW::where('id', $rw_id)->first();

        if (!$rwLocal) {
            return redirect()->back()->with('error', 'Local RW not found.');
        }

        $rw = $this->getRWbyUuid($rwLocal->uuid);

        if (!$rw || !isset($rw) || !isset($rw['id'])) {
            // Fallback to local RW data if API fails
            return redirect()->route('rw.index', [$desa_id, $rw_id])
                ->with('success', 'Failed file deleted successfully.');
        }

        return redirect()->route('rw.index', [$desa_id, $rw['id']])
            ->with('success', 'Failed file deleted successfully.');
    }
}
