import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

/// =============================
/// TEAMS LIST PAGE
/// =============================
class TeamPage extends StatelessWidget {
  const TeamPage({super.key});

  @override
  Widget build(BuildContext context) {
    const backgroundColor = Color(0xFFF6F4EF);
    const accentRed = Color(0xFF8B1E2D);
    const secondaryBlue = Color(0xFF163A6F);

    // 🔥 Mock teams (replace with DB later)
    final List<Map<String, String>> teams = [
      {
        "name": "U14 FC Rochester",
        "league": "Under 14 Division A",
        "coach": "Michael Thompson",
      },
      {
        "name": "U12 North Carolina FC",
        "league": "Under 12 Division B",
        "coach": "Sarah Johnson",
      },
      {
        "name": "U16 Dakota Elite",
        "league": "Under 16 Premier League",
        "coach": "Daniel Martinez",
      },
    ];

    return Scaffold(
      backgroundColor: backgroundColor,
      appBar: AppBar(
        backgroundColor: backgroundColor,
        elevation: 0,
        title: Text(
          'Teams',
          style: GoogleFonts.inter(
            fontWeight: FontWeight.w700,
            color: Colors.black,
          ),
        ),
      ),
      body: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: teams.length,
        itemBuilder: (context, index) {
          final team = teams[index];

          return GestureDetector(
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => TeamDetailsPage(team: team)),
              );
            },
            child: Container(
              margin: const EdgeInsets.only(bottom: 16),
              padding: const EdgeInsets.all(18),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [accentRed, secondaryBlue],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(20),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    team["name"]!,
                    style: GoogleFonts.inter(
                      fontSize: 18,
                      fontWeight: FontWeight.w700,
                      color: Colors.white,
                    ),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    team["league"]!,
                    style: GoogleFonts.inter(color: Colors.white70),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    "Coach: ${team["coach"]!}",
                    style: GoogleFonts.inter(color: Colors.white70),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}

/// =============================
/// TEAM DETAILS PAGE
/// =============================
class TeamDetailsPage extends StatelessWidget {
  final Map<String, String> team;

  const TeamDetailsPage({super.key, required this.team});

  @override
  Widget build(BuildContext context) {
    const backgroundColor = Color(0xFFF6F4EF);
    const accentRed = Color(0xFF8B1E2D);
    const secondaryBlue = Color(0xFF163A6F);

    return Scaffold(
      backgroundColor: backgroundColor,
      appBar: AppBar(
        backgroundColor: backgroundColor,
        elevation: 0,
        title: Text(
          team["name"]!,
          style: GoogleFonts.inter(
            fontWeight: FontWeight.w700,
            color: Colors.black,
          ),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // ===== HEADER CARD =====
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(20),
                gradient: const LinearGradient(
                  colors: [accentRed, secondaryBlue],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    team["name"]!,
                    style: GoogleFonts.inter(
                      fontSize: 22,
                      fontWeight: FontWeight.w700,
                      color: Colors.white,
                    ),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    "League: ${team["league"]!}",
                    style: GoogleFonts.inter(color: Colors.white70),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 24),

            _sectionTitle('Coach Information'),
            const SizedBox(height: 12),
            _infoCard(child: _infoRow('Coach', team["coach"]!)),

            const SizedBox(height: 24),

            _sectionTitle('Practice Schedule'),
            const SizedBox(height: 12),
            _infoCard(
              child: Column(
                children: [
                  _scheduleRow(
                    'Tuesday',
                    '5:30 PM - 7:00 PM',
                    'City Sports Field',
                  ),
                  _scheduleRow(
                    'Thursday',
                    '5:30 PM - 7:00 PM',
                    'City Sports Field',
                  ),
                ],
              ),
            ),

            const SizedBox(height: 24),

            _sectionTitle('Upcoming Game'),
            const SizedBox(height: 12),
            _infoCard(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    '${team["name"]!} vs Rival FC',
                    style: GoogleFonts.inter(fontWeight: FontWeight.w600),
                  ),
                  const SizedBox(height: 8),
                  _infoRow('Date', 'Saturday, March 23'),
                  _infoRow('Time', '2:00 PM'),
                  _infoRow('Location', 'North Carolina Stadium'),
                ],
              ),
            ),

            const SizedBox(height: 24),

            _sectionTitle('Coach Notes'),
            const SizedBox(height: 12),
            _infoCard(
              child: Text(
                'Please ensure all players arrive 30 minutes early for warm-up. '
                'Bring full uniform and water bottles. Let’s keep the energy high this week!',
                style: GoogleFonts.inter(),
              ),
            ),

            const SizedBox(height: 40),
          ],
        ),
      ),
    );
  }

  Widget _sectionTitle(String title) {
    return Text(
      title,
      style: GoogleFonts.inter(fontSize: 18, fontWeight: FontWeight.w700),
    );
  }

  Widget _infoCard({required Widget child}) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: child,
    );
  }

  Widget _infoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Row(
        children: [
          Text(
            '$label: ',
            style: GoogleFonts.inter(fontWeight: FontWeight.w600),
          ),
          Expanded(child: Text(value, style: GoogleFonts.inter())),
        ],
      ),
    );
  }

  Widget _scheduleRow(String day, String time, String location) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(day, style: GoogleFonts.inter(fontWeight: FontWeight.w600)),
          Text(time, style: GoogleFonts.inter()),
          Text(location, style: GoogleFonts.inter(color: Colors.grey[600])),
          const Divider(),
        ],
      ),
    );
  }
}
